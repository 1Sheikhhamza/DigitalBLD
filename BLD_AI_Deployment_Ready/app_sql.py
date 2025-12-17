import streamlit as st
import google.generativeai as genai
import mysql.connector
from mysql.connector import Error
import os
import re
import json
import logging
import base64
from pathlib import Path
from datetime import datetime
from dotenv import load_dotenv
from typing import Optional
import chromadb
from chromadb.utils import embedding_functions

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Page config
st.set_page_config(
    page_title="BLD.AI - Legal Assistant", 
    page_icon="⚖️", 
    layout="wide",
    initial_sidebar_state="collapsed"
)

# Load environment variables
load_dotenv()

# API Key configuration with fallback
api_key = os.getenv('GOOGLE_API_KEY')
if not api_key:
    api_key = "AIzaSyB_woqxl5V9HRhTmBw6B-TvU6Pp-hxhSIg"  # Fallback key

genai.configure(api_key=api_key)

# Initialize session state
if 'messages' not in st.session_state:
    st.session_state.messages = []
if 'api_calls' not in st.session_state:
    st.session_state.api_calls = 0
if 'db_initialized' not in st.session_state:
    st.session_state.db_initialized = False
if 'current_case' not in st.session_state:
    st.session_state.current_case = None
if 'latest_results' not in st.session_state:
    st.session_state.latest_results = []
if 'current_parties' not in st.session_state:
    st.session_state.current_parties = None
if 'theme' not in st.session_state:
    st.session_state.theme = 'light'  # Always light mode
if 'show_sources' not in st.session_state:
    st.session_state.show_sources = False

# Database Config (Dual Mode: Cloud + Local)
def get_db_config():
    # 1. Try Streamlit Secrets (Production)
    if "mysql" in st.secrets:
        return st.secrets["mysql"]
    
    # 2. Fallback to Localhost (Development)
    return {
        'host': 'localhost',
        'port': 3306,
        'database': 'bldlegalized_bld',
        'user': 'root',
        'password': '',
        'charset': 'utf8mb4',
        'use_unicode': True
    }

DB_CONFIG = get_db_config()

def get_db_connection():
    """Create MySQL database connection"""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        return conn
    except Error as e:
        logger.error(f"MySQL connection error: {e}")
        st.error(f"Database connection error: {e}")
        return None

def init_database():
    """Check MySQL database connection"""
    if st.session_state.db_initialized:
        return
    
    try:
        conn = get_db_connection()
        if conn and conn.is_connected():
            cursor = conn.cursor()
            cursor.execute("SELECT COUNT(*) FROM ocr_extractions")
            count = cursor.fetchone()[0]
            logger.info(f"✅ Connected to MySQL database with {count} cases")
            st.session_state.db_initialized = True
            cursor.close()
            conn.close()
    except Error as e:
        logger.error(f"Database initialization error: {e}")
        st.error(f"Database error: {e}. Make sure XAMPP MySQL is running!")

def extract_date_components(query: str) -> Optional[tuple[str, str, str]]:
    """Extracts day, month, year from query."""
    try:
        # Pattern 1: [The] 17th March, 1982 (or 17 March 1982)
        match1 = re.search(r'(?:The\s+)?(\d{1,2})(?:st|nd|rd|th)?\s+([A-Za-z]+)[,\s]+(\d{4})', query, re.IGNORECASE)
        if match1:
            return match1.groups() # (day, month, year)
        
        # Pattern 2: March 17, 1982
        match2 = re.search(r'([A-Za-z]+)\s+(\d{1,2})[,\s]+(\d{4})', query, re.IGNORECASE)
        if match2:
            month, day, year = match2.groups()
            return (day, month, year)

    except Exception as e:
        logger.error(f"Date extraction error: {e}")
    
    return None

def clean_legal_query(query: str) -> str:
    """Removes common legal titles and punctuation from query to assist AI."""
    # Common titles to strip (case insensitive)
    # Added 'J.' and 'J' which are common abbreviations for Justice
    titles = [r'\bC\.?J\.?,?', r'\bJustice\b', r'\bMr\.?\b', r'\bMrs\.?\b', r'\bDr\.?\b', r'\bAdvocate\b', r'\bJ\.?,?']
    cleaned = query
    for title in titles:
        cleaned = re.sub(title, '', cleaned, flags=re.IGNORECASE)
    
    # Remove punctuation from ends and multiple spaces
    cleaned = re.sub(r'[^\w\s]+$', '', cleaned)
    cleaned = re.sub(r'\s+', ' ', cleaned).strip()
    return cleaned

def generate_sql(question: str) -> str:
    """Generate SQL query from natural language question using Gemini with accurate schema"""
    
    # Build context-aware prompt
    context_info = ""
    
    # Build context-aware prompt
    context_info = ""
    if st.session_state.current_case:
        context_info = f"\nCurrent case context: {st.session_state.current_case}"
    
    # Complete and accurate schema with field descriptions
    schema_info = f"""Table: ocr_extractions

COMPLETE FIELD LIST:
- id: Primary key
- volume_id: Volume identifier
- book_volume: Volume number of BLD book
- judge_name: Name of the judge(s)
- content: Full text content
- file_path: Path to PDF file
- file_name: Name of PDF file
- status: Status of the record
- published_year: Year the case was published in BLD
- published_month: Month the case was published
- starting_page_no: Starting page number in BLD book
- ending_page_no: Ending page number in BLD book
- division: Division where case was heard (e.g., Appellate, High Court)
- decided_on: Judgment date (YYYY-MM-DD format)
- parties: Names of all parties involved in the case
- petitioners: Advocates/lawyers representing petitioners
- respondent: Advocates/lawyers representing respondents
- related_act_order_rule: Related acts, orders, or rules
- sections_subsections: Specific sections and subsections cited
- key_words: Important keywords from the case
- subject: Subject matter of the case
- result: Outcome/result of the case
- case_no: Case number (e.g., "Civil Appeal No. 582 of 2001")
- jurisdiction: Jurisdiction information
- judgment: Full judgment text
- homepage: Homepage flag{context_info}

IMPORTANT RULES:
1. Return ONLY SQL query, no explanation or markdown
2. Use LIKE '%keyword%' for text searches (case-insensitive)
3. For case numbers, search in case_no field
4. For judges, use judge_name field
5. For advocates/lawyers, search in petitioners OR respondent fields
6. For "published year", use published_year field
7. For date searches, use decided_on field
8. For acts/laws, search in related_act_order_rule field
9. For sections, search in sections_subsections field
10. For division (Appellate/High Court), use division field
11. For page numbers, use starting_page_no and ending_page_no
12. You can search in ANY relevant column from the field list
13. Select only relevant columns needed to answer the question
14. LIMIT 20 unless user asks for more
15. Use OR when searching multiple fields for same keyword
16. ALWAYS INCLUDE 'id', 'jurisdiction', 'book_volume', 'published_year', 'starting_page_no', 'ending_page_no', 'related_act_order_rule', and 'sections_subsections' columns in your SELECT statement
17. CORRECT TYPOS: If the user makes a spelling mistake (e.g., "cmmissioner", "incom-tax"), use the CORRECTED spelling (e.g., "commissioner", "income-tax") in your SQL LIKE clauses. Use your knowledge of legal terms and proper names to fix errors.
18. CONVERT DATES: Transform natural language dates like "17th March, 1982" into 'YYYY-MM-DD' format (e.g., '1982-03-17') for the `decided_on` column.
19. LOGICAL OPERATORS: When a user specifies multiple conditions (e.g., "Judge X AND Date Y"), use the `AND` operator to strictly require both.
20. CLEAN NAMES: When searching for Judges or Parties, REMOVE titles like 'C.J.', 'Justice', 'Mr.', 'Mrs.', 'Dr.', 'Advocate' and punctuation. Only search for the core name (e.g., "Kemaluddin Hossain C.J," -> `judge_name LIKE '%Kemaluddin Hossain%'`).

QUERY EXAMPLES:

Q: "Tell me about case 582 of 2001"
SQL: SELECT id, case_no, parties, division, decided_on, judge_name, subject, result FROM ocr_extractions WHERE case_no LIKE '%582%' AND case_no LIKE '%2001%' LIMIT 5;

Q: "Find Civil Revision No. 6085 of 2007"
SQL: SELECT id, case_no, parties, division, decided_on, judge_name, subject, result, petitioners, respondent FROM ocr_extractions WHERE case_no LIKE '%6085%' AND case_no LIKE '%2007%' LIMIT 5;

Q: "Who was the judge in this case?" (with context: case 582)
SQL: SELECT id, case_no, judge_name, decided_on FROM ocr_extractions WHERE case_no LIKE '%582%' LIMIT 5;

Q: "What sections were mentioned?"
SQL: SELECT id, case_no, sections_subsections, related_act_order_rule FROM ocr_extractions WHERE case_no LIKE '%{st.session_state.current_case if st.session_state.current_case else ''}%' LIMIT 5;

Q: "Cases involving Kabita Khatun"
SQL: SELECT case_no, parties, division, decided_on, subject FROM ocr_extractions WHERE parties LIKE '%Kabita Khatun%' LIMIT 5;

Q: "Show me cases about marriage"
SQL: SELECT case_no, parties, subject, result, decided_on FROM ocr_extractions WHERE subject LIKE '%marriage%' OR key_words LIKE '%marriage%' LIMIT 5;

Q: "Cases with Code of Civil Procedure"
SQL: SELECT case_no, parties, related_act_order_rule, sections_subsections, result FROM ocr_extractions WHERE related_act_order_rule LIKE '%Code of Civil Procedure%' LIMIT 5;

Q: "Cases decided by Appellate Division"
SQL: SELECT case_no, parties, division, decided_on, judge_name, subject FROM ocr_extractions WHERE division LIKE '%Appellate%' LIMIT 5;

Q: "Find cases from 2007"
SQL: SELECT case_no, parties, division, decided_on, subject, result FROM ocr_extractions WHERE published_year = 2007 LIMIT 5;

Q: "Cases decided in March 2006"
SQL: SELECT case_no, parties, decided_on, subject, result FROM ocr_extractions WHERE decided_on LIKE '2006-03-%' LIMIT 5;

Q: "Show me dismissed appeals"
SQL: SELECT case_no, parties, result, decided_on, division FROM ocr_extractions WHERE result LIKE '%dismiss%' LIMIT 5;

Q: "Cases where Advocate Rahman appeared"
SQL: SELECT case_no, parties, petitioners, respondent, decided_on FROM ocr_extractions WHERE petitioners LIKE '%Rahman%' OR respondent LIKE '%Rahman%' LIMIT 5;

Q: "Who was the petitioner?"
SQL: SELECT id, case_no, petitioners, parties FROM ocr_extractions WHERE case_no LIKE '%{st.session_state.current_case if st.session_state.current_case else ''}%' LIMIT 5;

Q: "Who was the advocate in case 3034 of 1999?"
SQL: SELECT case_no, parties, petitioners, respondent, division, decided_on FROM ocr_extractions WHERE case_no LIKE '%3034%' AND case_no LIKE '%1999%' LIMIT 5;

Q: "Tell me about writ petition 3034 of 1999 and who was the advocate"
SQL: SELECT case_no, parties, petitioners, respondent, division, decided_on, judge_name, subject, result FROM ocr_extractions WHERE case_no LIKE '%3034%' AND case_no LIKE '%1999%' LIMIT 5;

Q: "Cases in volume 59"
SQL: SELECT case_no, parties, book_volume, starting_page_no, ending_page_no, subject FROM ocr_extractions WHERE book_volume = 59 LIMIT 5;

Q: "Natural justice cases"
SQL: SELECT case_no, parties, subject, key_words, result FROM ocr_extractions WHERE subject LIKE '%natural justice%' OR key_words LIKE '%natural justice%' LIMIT 5;

Question: {question}
SQL:"""
    
    # PREPARE PROMPT WITH HINTS
    # We inject hints directly into the question block to ensure maximizing attention
    prompt_hints = ""
    
    # 1. Date Hint
    date_components = extract_date_components(question)
    if date_components:
        day, month, year = date_components
        # Database stores strings like "The 10 of May, 2006" or similar variations.
        # Best strategy: LIKE '%10%May%2006%' OR LIKE '%May%10%2006%'
        prompt_hints += f"\n[SYSTEM HINT: Detected date '{day} {month} {year}'. Database stores unstructured date strings. MUST USE WILDCARD: `decided_on` LIKE '%{day}%{month}%{year}%' OR `decided_on` LIKE '%{month}%{day}%{year}%']"

    # 2. Name Cleaning Hint
    cleaned_q = clean_legal_query(question)
    if cleaned_q != question and len(cleaned_q) > 5:
         prompt_hints += f"\n[SYSTEM HINT: Cleaned name query: '{cleaned_q}'. Search for this name/parties.]"

    if st.session_state.current_case:
        prompt_hints += f"\n[CONTEXT: Current case is {st.session_state.current_case}]"
    if st.session_state.current_parties:
        prompt_hints += f"\n[CONTEXT: Current parties are {st.session_state.current_parties}]"
    
    final_prompt = f"Question: {question}{prompt_hints}\n\n{schema_info}"
    
    try:
        model = genai.GenerativeModel("gemini-2.0-flash-exp")
        response = model.generate_content(
            final_prompt,
            generation_config=genai.types.GenerationConfig(
                temperature=0.1,  # Low temperature for deterministic SQL
                max_output_tokens=200
            )
        )
        st.session_state.api_calls += 1
        
        sql = response.text.strip()
        sql = sql.replace('```sql', '').replace('```', '').strip()
    
        # Force inclusion of 'id' and 'jurisdiction' columns
        if "SELECT" in sql.upper():
            if "id" not in sql.lower().split("from")[0]:
                sql = re.sub(r'SELECT\s+', 'SELECT id, ', sql, count=1, flags=re.IGNORECASE)
        if "SELECT" in sql.upper() and "jurisdiction" not in sql.lower().split("from")[0]:
            sql = re.sub(r'SELECT\s+', 'SELECT jurisdiction, ', sql, count=1, flags=re.IGNORECASE)

        # Force inclusion of citation fields
        extra_fields = ['book_volume', 'published_year', 'starting_page_no', 'ending_page_no', 'related_act_order_rule', 'sections_subsections']
        for field in extra_fields:
             if "SELECT" in sql.upper() and field not in sql.lower().split("from")[0]:
                sql = re.sub(r'SELECT\s+', f'SELECT {field}, ', sql, count=1, flags=re.IGNORECASE)
        
        logger.info(f"Generated SQL: {sql}")
        return sql
    except Exception as e:
        logger.error(f"SQL generation error: {e}")
        return None

def perform_semantic_search(query: str, limit: int = 5) -> list:
    """Perform semantic search using ChromaDB"""
    try:
        chroma_client = chromadb.PersistentClient(path="./chroma_db")
        collection = chroma_client.get_or_create_collection(name="bld_cases")
        
        results = collection.query(
            query_texts=[query],
            n_results=limit
        )
        
        # Format results to match SQL result structure
        formatted_results = []
        if results['ids'] and results['ids'][0]:
            for i, case_id in enumerate(results['ids'][0]):
                meta = results['metadatas'][0][i]
                doc = results['documents'][0][i]
                formatted_results.append({
                    'id': case_id,
                    'case_no': meta.get('case_no'),
                    'judge_name': meta.get('judge'),
                    'parties': meta.get('parties'),
                    'published_year': meta.get('year'),
                    'subject': doc.split('\\n')[0].replace('Subject: ', '') if 'Subject:' in doc else 'N/A',
                    'source': 'semantic' # tag ensuring UI knows this is from vector
                })
        return formatted_results
    except Exception as e:
        logger.error(f"Semantic search error: {e}")
        return []

def execute_sql(sql: str):
    """Execute SQL query and return results"""
    try:
        conn = get_db_connection()
        if not conn:
            return None
            
        cursor = conn.cursor(dictionary=True)
        cursor.execute(sql)
        
        # Fetch results
        results = cursor.fetchall()
        cursor.close()
        conn.close()
        
        return results
    except Error as e:
        logger.error(f"SQL execution error: {e}")
        return None

def search_in_judgment(question: str, case_filter: str = None) -> tuple:
    """Fallback: Search in judgment text when SQL doesn't find answer"""
    try:
        conn = get_db_connection()
        if not conn:
            return None, None
            
        cursor = conn.cursor(dictionary=True)
        
        # Build query to search in judgment
        if case_filter:
            sql = f"SELECT case_no, parties, judgment FROM ocr_extractions WHERE {case_filter}"
        else:
            sql = "SELECT case_no, parties, judgment FROM ocr_extractions WHERE judgment IS NOT NULL LIMIT 5"
        
        cursor.execute(sql)
        results = cursor.fetchall()
        cursor.close()
        conn.close()
        
        if not results:
            return None, None
        
        # Search for relevant text in judgment (simple keyword matching)
        keywords = question.lower().split()
        best_match = None
        best_score = 0
        
        for row in results:
            judgment = row.get('judgment', '')
            if not judgment:
                continue
            judgment_lower = judgment.lower()
            score = sum(1 for kw in keywords if kw in judgment_lower and len(kw) > 3)
            if score > best_score:
                best_score = score
                best_match = (row['case_no'], row['parties'], judgment)
        
        return best_match, best_score
    except Error as e:
        logger.error(f"Judgment search error: {e}")
        return None, None

def generate_answer(question: str, results: list, used_fallback: bool = False) -> str:
    """Generate human-readable answer from SQL results with smart fallback"""
    
    # Check if we need fallback to judgment search
    if not results or len(results) == 0:
        logger.info("No SQL results, trying judgment search fallback...")
        
        # Try to extract case info from question
        case_filter = None
        if st.session_state.current_case:
            case_filter = f"case_no LIKE '%{st.session_state.current_case}%'"
        elif "case" in question.lower() and any(char.isdigit() for char in question):
            # Extract numbers from question
            numbers = re.findall(r'\d+', question)
            if numbers:
                case_filter = f"case_no LIKE '%{numbers[0]}%'"
        
        match, score = search_in_judgment(question, case_filter)
        
        if match and score > 0:
            case_no, parties, judgment = match
            # Extract relevant portion (first 1000 chars containing keywords)
            keywords = [w for w in question.lower().split() if len(w) > 3]
            judgment_lower = judgment.lower()
            
            # Find best starting position
            best_pos = 0
            for kw in keywords:
                pos = judgment_lower.find(kw)
                if pos > 0:
                    best_pos = max(0, pos - 200)
                    break
            
            relevant_text = judgment[best_pos:best_pos + 1000]
            
            # Generate concise answer from judgment
            prompt = f"""Based on this judgment excerpt, answer briefly (max 100 words):

Question: {question}
Case: {case_no}
Parties: {parties}

Judgment excerpt:
{relevant_text}

Answer (be concise):"""
            
            try:
                model = genai.GenerativeModel("gemini-2.0-flash-exp")
                response = model.generate_content(
                    prompt,
                    generation_config=genai.types.GenerationConfig(
                        temperature=0.3,
                        max_output_tokens=150  # Short answers only
                    )
                )
                st.session_state.api_calls += 1
                return f"📄 **From Judgment Search:**\n\n{response.text.strip()}\n\n*Case: {case_no}*"
            except Exception as e:
                logger.error(f"Fallback answer error: {e}")
                return "Could not find answer in available cases."
        else:
            return "No cases found matching your query. Try rephrasing or ask about a different case."
    
    # Update context if we found a case
    if results and len(results) > 0:
        first_result = results[0]
        if 'case_no' in first_result and first_result['case_no']:
            st.session_state.current_case = first_result['case_no']
        if 'parties' in first_result and first_result['parties']:
            st.session_state.current_parties = first_result['parties']
    
    # Format results for AI (token-efficient)
    results_text = ""
    # Smart context management: If many results, strip heavy text fields to avoid token errors
    include_full_text = len(results) <= 3
    
    for i, result in enumerate(results, 1):  # Process ALL results
        results_text += f"\n--- Case {i} ---\n"
        for key, value in result.items():
            # Skip massive text fields if we have many results, unless specifically needed
            if not include_full_text and key in ['content', 'judgment', 'file_path']:
                continue
                
            if value and key in ['case_no', 'parties', 'petitioners', 'respondent', 
                                 'division', 'decided_on', 'judge_name', 'subject', 'result',
                                 'sections_subsections', 'related_act_order_rule', 'volume_id', 'book_volume', 'published_year',
                                 'published_month', 'starting_page_no', 'ending_page_no', 'key_words', 'jurisdiction', 
                                 'content', 'judgment', 'file_name', 'status', 'id', 'homepage']:
                # Do not truncate - User requested full text
                # if isinstance(value, str) and len(value) > 300:
                #     value = value[:300] + "..."
                results_text += f"{key}: {value}\n"
    
    # Answer prompt
    prompt = f"""Answer the question based on the database results.
    
IMPORTANT FORMATTING:
- **PAGE FORMAT**: Combine starting and ending pages into a single line: **Page:** [start] to [end] (e.g. **Page:** 23 to 25).
- **CLEAN DATA**: Remove all HTML tags (<p>, <br>).

ANSWERING RULES:
1. **SPECIFIC QUESTIONS** (e.g., "Who is the judge?", "What is the date?"):
   - ANSWER ONLY THE SPECIFIC QUESTION.
   - Do NOT list Case No, Volume, Year, or Parties unless explicitly asked.
   - Example Q: "Who is the judge?" -> A: "The judge was **Justice Mr. X**." (That's it).

2. **GENERAL SEARCH / DETAILS** (e.g., "Tell me about case X", "Find cases about land"):
   - Provide a structured summary with bullet points.
   - **MANDATORY**: You MUST Display **Volume**, **Year**, **Page**, **Related Acts**, and **Sections** if available.
   - Bold the field labels (e.g., **Case No:**).

IMPORTANT CONTEXT: 
- If 'petitioners' field is present, it contains the advocates/lawyers for the petitioners
- If 'respondent' field is present, it contains the advocates/lawyers for the respondents
- When asked about advocates/lawyers, ALWAYS mention the names from these fields

Q: {question}
{results_text}

Answer:"""
    
    try:
        if st.session_state.api_calls >= 5000:
            st.warning("High API usage detected. Please use responsibly.")
            
        model = genai.GenerativeModel("gemini-2.0-flash-exp")
        response = model.generate_content(
            prompt,
            generation_config=genai.types.GenerationConfig(
                temperature=0.3,
                max_output_tokens=8000  # Increased for full details
            )
        )
        st.session_state.api_calls += 1
        
        return response.text.strip()
    except Exception as e:
        logger.error(f"Answer generation error: {e}")
        return f"Found {len(results)} case(s) but couldn't generate answer."

# Custom CSS for Doraemon + Law Theme
def apply_custom_css():
    # Permanent Light Theme
    primary_bg = "#e6f3ff"  # Light blue background
    secondary_bg = "#ffffff"
    accent_color = "#33aaff"  # Bright light blue accent
    text_color = "#000000"    # Pure black for visibility
    card_bg = "#ffffff"
    border_color = "#b3d9ff"
    header_bg = "#f0f8ff"
    
    st.markdown(f"""
    <style>
        /* Force override Streamlit's default theme */
        :root {{
            --background-color: {primary_bg};
            --secondary-background-color: {secondary_bg};
            --text-color: {text_color};
        }}
        
        /* Main container */
        .stApp {{
            background: linear-gradient(135deg, {primary_bg} 0%, {secondary_bg} 100%) !important;
            color: {text_color} !important;
        }}
        
        /* Force text color on all reasonable elements */
        p, h1, h2, h3, h4, h5, h6, span, div, label {{
            color: {text_color} !important;
        }}
        
        /* Fix header toolbar (top black bar) */
        header[data-testid="stHeader"] {{
            background-color: {header_bg} !important;
            border-bottom: 1px solid {border_color} !important;
        }}
        
        /* Fix bottom container (input area) */
        .stBottom, [data-testid="stBottom"] {{
            background-color: {secondary_bg} !important;
            border-top: 1px solid {border_color} !important;
        }}
        
        /* Force bottom container children to match */
        [data-testid="stBottom"] > div {{
            background-color: {secondary_bg} !important;
        }}

        /* Extra forceful override for deployed environments */
        div[class*="stBottom"] {{
            background-color: {secondary_bg} !important;
        }}
        
        /* Main content area */
        .main .block-container {{
            background-color: transparent !important;
            padding-top: 5rem !important;
            padding-bottom: 10rem !important;
            max-width: 100% !important;
        }}
        
        /* Header styling */
        .main-header {{
            text-align: center;
            padding: 2rem 0;
            background: linear-gradient(135deg, {accent_color}22 0%, {accent_color}11 100%);
            border-radius: 15px;
            margin-bottom: 2rem;
            border: 2px solid {accent_color}44;
        }}
        
        .main-title {{
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, {accent_color} 0%, #ff6b9d 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            font-family: 'Segoe UI', sans-serif;
        }}
        
        .sub-title {{
            color: {text_color};
            font-size: 1.2rem;
            opacity: 0.8;
        }}
        

        
        /* Sidebar */
        [data-testid="stSidebar"] {{
            background-color: {secondary_bg} !important;
            border-right: 2px solid {accent_color}33;
        }}

        /* Justify text in chat messages */
        .stChatMessage p, .stChatMessage li {{
            text-align: justify !important;
        }}
        
        [data-testid="stSidebar"] > div:first-child {{
            background-color: {secondary_bg} !important;
        }}
        
        /* Fix sidebar text color */
        [data-testid="stSidebar"] * {{
            color: {text_color} !important;
        }}
        
        /* Buttons */
        .stButton > button {{
            background: linear-gradient(135deg, {accent_color} 0%, #0066aa 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: 20px;
            padding: 0.25rem 0.75rem;
            font-weight: 600;
            font-size: 0.9rem;
            min-height: 38px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }}
        
        /* Fixed Position Buttons Removed - Reverted to standard flow */
        
        .stButton > button:hover {{
            transform: translateY(-2px);
            box-shadow: 0 5px 15px {accent_color}44;
        }}
        
        .stButton > button:disabled {{
            background: {border_color} !important;
            opacity: 0.6;
        }}
        
        /* NUCLEAR OPTION: Universal override for Chat Input */
        [data-testid="stChatInput"] {{
            background-color: #ffffff !important;
            border-radius: 20px !important;
            border: 2px solid {accent_color} !important;
            padding: 5px !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
        }}
        
        /* Force EVERY element inside the chat input to be white/black */
        [data-testid="stChatInput"] * {{
            background-color: #ffffff !important;
            color: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
        }}
        
        /* Exception: Placeholder needs to be gray */
        [data-testid="stChatInput"] textarea::placeholder {{
            color: #666666 !important;
            -webkit-text-fill-color: #666666 !important;
        }}
        
        /* Exception: Send button needs distinct color on hover if possible, 
           but for now let's just make sure it's visible (black icon on white bg) */
        [data-testid="stChatInput"] button {{
            border: none !important;
        }}
        
        /* Metrics */
        [data-testid="stMetricValue"] {{
            color: {accent_color} !important;
            font-weight: 700;
        }}
        
        [data-testid="stMetricLabel"] {{
            color: {text_color} !important;
        }}
        
        /* Expander */
        .streamlit-expanderHeader {{
            background-color: {card_bg} !important;
            border-radius: 8px;
            border: 1px solid {border_color};
            color: {text_color} !important;
        }}
        
        /* Code blocks */
        .stCodeBlock {{
            background-color: {secondary_bg} !important;
            border: 1px solid {border_color} !important;
            border-radius: 8px !important;
        }}
        
        /* Markdown text */
        .stMarkdown {{
            color: {text_color} !important;
        }}
        
        /* Divider */
        hr {{
            border-color: {border_color} !important;
        }}
        
        /* Info/Success boxes */
        .stAlert {{
            background-color: {card_bg} !important;
            border: 1px solid {border_color} !important;
            color: {text_color} !important;
        }}
        
        /* ============================================ */
        /* MOBILE RESPONSIVE DESIGN */
        /* ============================================ */
        
        /* Mobile: screens smaller than 768px */
        @media (max-width: 768px) {{
            /* Header adjustments */
            .main-header {{
                padding: 1.5rem 0.5rem;
                margin-bottom: 1rem;
            }}
            
            .main-title {{
                font-size: 2rem !important;
            }}
            
            .sub-title {{
                font-size: 0.9rem !important;
            }}
            
            /* Main content padding */
            .main .block-container {{
                padding: 1rem 0.5rem !important;
            }}
            
            /* Chat messages */
            .stChatMessage {{
                padding: 0.75rem !important;
                font-size: 0.9rem;
            }}
            
            /* Buttons - larger touch targets */
            .stButton > button {{
                padding: 0.75rem 1rem !important;
                font-size: 1rem;
                min-height: 44px;
            }}
            
            /* Input box - larger for mobile */
            .stChatInputContainer {{
                font-size: 16px !important;
            }}
            
            /* Sidebar adjustments */
            [data-testid="stSidebar"] {{
                width: 280px !important;
            }}
            
            /* Metrics - stack vertically */
            [data-testid="stMetric"] {{
                margin-bottom: 0.5rem;
            }}
            
            /* Code blocks - scrollable */
            .stCodeBlock {{
                overflow-x: auto;
                font-size: 0.85rem;
            }}
        }}
        
        /* Tablet: screens between 768px and 1024px */
        @media (min-width: 768px) and (max-width: 1024px) {{
            .main-title {{
                font-size: 2.5rem !important;
            }}
            
            .sub-title {{
                font-size: 1rem !important;
            }}
            
            .main .block-container {{
                padding: 1.5rem 1rem !important;
            }}
        }}
        
        /* Touch-friendly improvements */
        @media (hover: none) and (pointer: coarse) {{
            /* Increase button size for touch */
            .stButton > button {{
                min-height: 48px;
                padding: 0.75rem 1.25rem !important;
            }}
            
            /* Larger tap targets */
            .streamlit-expanderHeader {{
                min-height: 48px;
                padding: 0.75rem !important;
            }}
        }}
        
        /* Landscape mobile */
        @media (max-width: 768px) and (orientation: landscape) {{
            .main-header {{
                padding: 1rem 0.5rem;
            }}
            
            .main-title {{
                font-size: 1.75rem !important;
            }}
        }}
    </style>
    """, unsafe_allow_html=True)

apply_custom_css()

# Top Control Bar
with st.container():
    col1, col2, col3 = st.columns([18, 1, 1])
    
    with col2:
        # Toggle Sources Button
        icon = "📖" if st.session_state.show_sources else "📕"
        if st.button(icon, help="Toggle Sources Panel", use_container_width=True):
            st.session_state.show_sources = not st.session_state.show_sources
            st.rerun()

    with col3:
        # Clear Chat Button (Top Right - beside Deploy)
        if st.button("🗑️", help="Clear Conversation", use_container_width=True):
            st.session_state.messages = []
            st.session_state.current_case = None
            st.session_state.current_parties = None
            st.rerun()
    
# Initialize database
init_database()



if 'view_case_id' not in st.session_state:
    st.session_state.view_case_id = None
# Main Layout: Chat (Left) vs Sources (Right)
if st.session_state.show_sources:
    col_chat, col_sources = st.columns([7, 3])
else:
    col_chat = st.container()
    col_sources = None

with col_chat:


    # Display chat messages
    for message in st.session_state.messages:
        with st.chat_message(message["role"]):
            st.markdown(message["content"])

# Right-Side Panel for Sources
if col_sources:
    with col_sources:
        count = len(st.session_state.latest_results) if st.session_state.latest_results else 0
        st.markdown(f"### 📚 Source Cases ({count})")
        st.markdown("---")
        
        if st.session_state.latest_results:
            for i, res in enumerate(st.session_state.latest_results, 1):
                # Data for card
                case_id = res.get('id')
                case_no = res.get('case_no', 'N/A')
                parties = res.get('parties', 'Parties N/A')
                year = res.get('published_year')
                if not year or year == 'N/A':
                    year = res.get('decided_on', 'Year N/A')

                # STREAMLIT BUTTON CARD (Reliable)
                with st.container():
                    st.markdown(f"""
                    <div style="
                        background-color: white;
                        border: 1px solid #ddd;
                        border-radius: 10px;
                        padding: 15px;
                        margin-bottom: 15px;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                        transition: transform 0.2s;
                    ">
                        <div style="font-weight:bold; color:#0066cc; margin-bottom:5px;">{case_no}</div>
                        <div style="font-size:0.9em;">{parties[:60]}...</div>
                        <div style="font-size:0.8em; color:gray;">{res.get('jurisdiction', 'Jurisdiction N/A')} | {year}</div>
                        <div style="font-size:0.7em; color:#aaa; text-align:right;">ID: {case_id}</div>
                    </div>
                    """, unsafe_allow_html=True)
                    
                    # Direct Link to Laravel View
                    laravel_base_url = "http://127.0.0.1:8000" # Localhost base
                    case_url = f"{laravel_base_url}/subscriber/singleDecision/{case_id}"
                    
                    st.markdown(f'''
                        <a href="{case_url}" target="_blank" style="
                            display: inline-block;
                            width: 100%;
                            text-align: center;
                            padding: 0.5rem 1rem;
                            background: linear-gradient(135deg, #33aaff 0%, #0066aa 100%);
                            color: white;
                            text-decoration: none;
                            border-radius: 20px;
                            font-weight: 600;
                            margin-top: -10px;
                            margin-bottom: 10px;
                            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                        ">View Original Case ↗</a>
                    ''', unsafe_allow_html=True)

        else:
            st.info("Search for cases to see sources here.")

# Chat input (Global Bottom)
if prompt := st.chat_input("Ask about a legal case..."):
    # Add user message
    st.session_state.messages.append({"role": "user", "content": prompt})
    with st.chat_message("user"):
        st.markdown(prompt)
    
    # Generate SQL
    with st.spinner("Generating SQL query..."):
        sql = generate_sql(prompt)
    
        if sql:
            # Execute SQL
            with st.spinner("Searching database..."):
                results = execute_sql(sql)
                
                # Perform Semantic Search (Hybrid Approach)
                try:
                    semantic_results = perform_semantic_search(prompt, limit=5)
                    if results is None:
                        results = []
                    
                    # Deduplicate and Append
                    existing_ids = set()
                    for r in results:
                        existing_ids.add(str(r.get('id'))) 
                    
                    for res in semantic_results:
                        if str(res['id']) not in existing_ids:
                            results.append(res)
                            
                except Exception as e:
                    logger.error(f"Hybrid search error: {e}")
            
            if results:
                # SAVE RESULTS TO SESSION STATE FOR RIGHT PANEL
                st.session_state.latest_results = results
                
                # Generate Answer
                with st.spinner("Analyzing case data..."):
                    answer = generate_answer(prompt, results)
                
                # Add assistant message
                st.session_state.messages.append({
                    "role": "assistant", 
                    "content": answer,
                    "sql": sql
                })
                
                # Rerun to update the Right Panel immediately
                st.rerun()
            else:
                # Differentiate between Error (None) and No Results ([])
                if results is None:
                     error_msg = "Error executing SQL query. Please try rephrasing your question."
                     st.session_state.messages.append({"role": "assistant", "content": error_msg})
                     with st.chat_message("assistant"):
                         st.error(error_msg)
                else:
                     warning_msg = "No matching cases found. Try broadening your search or checking spelling."
                     st.session_state.messages.append({"role": "assistant", "content": warning_msg})
                     with st.chat_message("assistant"):
                         st.warning(warning_msg)
        else:
            error_msg = "Error generating SQL query. Please try again."
            st.session_state.messages.append({"role": "assistant", "content": error_msg})
            with st.chat_message("assistant"):
                st.error(error_msg)

# Footer

