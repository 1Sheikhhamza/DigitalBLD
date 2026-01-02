import streamlit as st
import requests  # Replaces google.generativeai
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
import uuid
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

# API Key configuration
api_key = os.getenv('GEMINI_API_KEY')
if not api_key:
    api_key = os.getenv('GOOGLE_API_KEY')
if not api_key:
    st.error("Missing API Key! Please set GEMINI_API_KEY in .env file.")
    st.stop()

# Direct API Helper
import time

def call_gemini_api(prompt, model="gemini-2.0-flash-001", max_tokens=1000):
    url = f"https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent?key={api_key}"
    headers = {"Content-Type": "application/json"}
    
    # Disable safety filters for legal/court content
    safety_settings = [
        {"category": "HARM_CATEGORY_HARASSMENT", "threshold": "BLOCK_NONE"},
        {"category": "HARM_CATEGORY_HATE_SPEECH", "threshold": "BLOCK_NONE"},
        {"category": "HARM_CATEGORY_SEXUALLY_EXPLICIT", "threshold": "BLOCK_NONE"},
        {"category": "HARM_CATEGORY_DANGEROUS_CONTENT", "threshold": "BLOCK_NONE"}
    ]
    
    payload = {
        "contents": [{"parts": [{"text": prompt}]}],
        "safetySettings": safety_settings,
        "generationConfig": {
            "temperature": 0.1,
            "maxOutputTokens": max_tokens
        }
    }
    
    for attempt in range(3):
        try:
            response = requests.post(url, headers=headers, json=payload, timeout=30)
            
            if response.status_code == 200:
                data = response.json()
                try:
                    return data['candidates'][0]['content']['parts'][0]['text']
                except (KeyError, IndexError):
                    logger.error(f"Malformed Response: {json.dumps(data)}")
                    if "usageMetadata" in data and "candidates" not in data:
                        return "Error: AI blocked the response content."
                    return None
                
            elif response.status_code == 429:
                wait_time = 2**(attempt+1)
                logger.warning(f"Rate limit (429). Retrying in {wait_time}s...")
                time.sleep(wait_time)
                continue
                
            else:
                logger.error(f"API Error {response.status_code}: {response.text}")
                return None
                
        except Exception as e:
            logger.error(f"Request failed: {e}")
            return None
            
    return None

# Initialize session state
if 'messages' not in st.session_state:
    st.session_state.messages = []
if 'api_calls' not in st.session_state:
    st.session_state.api_calls = 0
if 'db_initialized' not in st.session_state:
    st.session_state.db_initialized = False
if 'current_case' not in st.session_state:
    st.session_state.current_case = None
if 'current_case_id' not in st.session_state:
    st.session_state.current_case_id = None
if 'latest_results' not in st.session_state:
    st.session_state.latest_results = []
if 'current_parties' not in st.session_state:
    st.session_state.current_parties = None
if 'theme' not in st.session_state:
    st.session_state.theme = 'light'  # Always light mode
if 'show_sources' not in st.session_state:
    st.session_state.show_sources = False
if 'context_locked' not in st.session_state:
    st.session_state.context_locked = False
if 'user_id' not in st.session_state:
    st.session_state.user_id = None
if 'current_session_id' not in st.session_state:
    st.session_state.current_session_id = str(uuid.uuid4())
if 'history_loaded' not in st.session_state:
    st.session_state.history_loaded = False

# CHECK URL PARAMETERS FOR CONTEXT INJECTION (Integration point with Laravel)
# Using st.query_params (Streamlit new versions) or experimental_get_query_params
try:
    query_params = st.query_params
except:
    query_params = st.experimental_get_query_params()

if "case_id" in query_params:
    case_id_param = query_params["case_id"]
    # Handle list or string return type depending on version
    if isinstance(case_id_param, list):
         case_id_param = case_id_param[0]
         
    if st.session_state.current_case_id != case_id_param:
        st.session_state.current_case_id = case_id_param
        st.session_state.context_locked = True
        # Clear previous chat if switching cases
        st.session_state.messages = [] 
        st.session_state.history_loaded = False # Force reload history for new case/session context if needed
        logger.info(f"Context locked to Case ID: {case_id_param}")

def check_and_unlock_context(query: str):
    """
    Intelligent Context Unlocking:
    If the user asks about a SPECIFIC different case (e.g. "Writ Petition 1034"),
    we must UNLOCK the previous context to avoid SQL injection of the old case ID.
    """
    if not st.session_state.current_case:
        return

    # Pattern for explicit case references
    # e.g. "Civil Revision No 123", "Writ Petition 55", "Case 33 of 2022"
    case_patterns = [
        r"(?:civil|criminal|writ|appeal|revision|reference|case)\s+(?:petition|revision|appeal|reference|no\.?|number)?\s*\d+",
        r"case\s+no\.?\s*\d+",
        r"no\.?\s*\d+\s+of\s+\d{4}" 
    ]
    
    query_lower = query.lower()
    
    # If explicitly asking to "find", "search", "show" -> Unlock
    if any(x in query_lower for x in ["find ", "search ", "show ", "look for "]):
        logger.info("Context Unlocked due to explicit search keyword")
        st.session_state.context_locked = False
        st.session_state.current_case = None
        st.session_state.current_case_id = None
        st.session_state.current_parties = None
        return

    # If mentioning a specific case number that is DIFFERENT from current
    for pattern in case_patterns:
        if re.search(pattern, query_lower):
            # Check if it's just mentioning the current case?
            # heuristic: if detecting a case pattern, safest to UNLOCK to allow fresh SQL generation.
            # The generate_sql prompt will handle if it's the same case or new.
            # But we must stop the "SQL ENFORCER" from forcefully re-injecting the old one.
            logger.info("Context Unlocked due to detection of Case Number pattern in query")
            st.session_state.context_locked = False
            st.session_state.current_case = None
            st.session_state.current_case_id = None
            st.session_state.current_parties = None
            return

if "user_id" in query_params:
    user_id_param = query_params["user_id"]
    if isinstance(user_id_param, list):
        user_id_param = user_id_param[0]
    st.session_state.user_id = user_id_param

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
    if st.session_state.db_initialized and not st.session_state.context_locked:
        return
    
    try:
        conn = get_db_connection()
        if conn and conn.is_connected():
            cursor = conn.cursor(dictionary=True)
            
            # If we have a locked context (case_id from URL), fetch that case IMMEDIATELY
            if st.session_state.context_locked and st.session_state.current_case_id:
                # Reuse the logic to fetch case details
                cursor.execute(f"SELECT * FROM ocr_extractions WHERE id = {st.session_state.current_case_id}")
                case_data = cursor.fetchone()
                
                if case_data:
                    st.session_state.current_case = case_data['case_no']
                    st.session_state.current_parties = case_data['parties']
                    # Populate latest_results so the Sources panel shows this case
                    case_data['source'] = 'context_locked'
                    st.session_state.latest_results = [case_data]
                    st.session_state.db_initialized = True
                    logger.info(f"✅ Context Loaded: {st.session_state.current_case}")
                else:
                    logger.warning(f"❌ Case ID {st.session_state.current_case_id} not found.")

            else:
                 cursor.execute("SELECT COUNT(*) as count FROM ocr_extractions")
                 count = cursor.fetchone()['count'] # dictionary=True
                 logger.info(f"✅ Connected to MySQL database with {count} cases")
                 st.session_state.db_initialized = True
            
            # --- CHAT HISTORY TABLE INIT ---
            cursor.execute("""
                CREATE TABLE IF NOT EXISTS ai_chat_histories (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    session_id VARCHAR(255) DEFAULT NULL,
                    role VARCHAR(50) NOT NULL,
                    content TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            """)
            conn.commit()
            
            # --- LOAD HISTORY IF USER ID PRESENT ---
            # DISABLED: User requested to ALWAYS start with a CLEAN PAGE (New Chat)
            # if st.session_state.user_id and not st.session_state.history_loaded:
            #     # ... (Auto-load logic removed) ...
            #     pass
            
            st.session_state.history_loaded = True 


            cursor.close()
            conn.close()
    except Error as e:
        logger.error(f"Database initialization error: {e}")
        st.error(f"Database error: {e}. Make sure XAMPP MySQL is running!")

def save_chat_message(user_id, role, content, session_id=None):
    """Save chat message to database"""
    if not user_id:
        return
        
    try:
        conn = get_db_connection()
        if conn and conn.is_connected():
            cursor = conn.cursor()
            # Escape content to prevent SQL projection issues (though binding is safer, simple string escape for now)
            # Actually, let's use parameterized query which is standard
            sql = "INSERT INTO ai_chat_histories (user_id, role, content, session_id) VALUES (%s, %s, %s, %s)"
            cursor.execute(sql, (user_id, role, content, session_id))
            conn.commit()
            cursor.close()
            conn.close()
    except Error as e:
        logger.error(f"Failed to save chat history: {e}")

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

def classify_query_intent(query: str) -> str:
    """Classify if query is SQL-based (specific) or Semantic (narrative/situation)."""
    query_lower = query.lower()
    
    # 1. SQL Indicators (Specific Fields)
    sql_triggers = [
        "case no", "case number", "v.", "vs", "versus", "judge", "justice", 
        "date", "decided on", "volume", "page", "section", "article", "act",
        "find case", "search for", "show me", "list cases", "parties",
        "petitioner", "respondent"
    ]
    
    # 2. Check for SQL patterns
    if any(trigger in query_lower for trigger in sql_triggers):
        return "SQL"
        
    # 3. Check for Date patterns
    if extract_date_components(query):
        return "SQL"

    # 4. Check for Numbers (often implies Case No or Year) - BUT context matters
    # "3 people stole" -> Semantic. "Case 3 of 2022" -> SQL.
    # Simple heuristic: If number is present but no "case"/"section"/"act" context, it might be situation?
    # Actually, let's look for NARRATIVE indicators.
    
    # 5. Semantic/Narrative Indicators
    # Long queries (> 10 words) without specific SQL keywords are likely narratives
    word_count = len(query.split())
    if word_count > 8 and not any(x in query_lower for x in ["case", "section", "act", "volume"]):
        return "SEMANTIC"
        
    narrative_triggers = [
        "situation", "scenario", "happened", "story", "like", "similar to",
        "someone", "person", "theif", "robber", "killed", "murdered", "stole",
        "describe", "explain"
    ]
    if any(trigger in query_lower for trigger in narrative_triggers):
        return "SEMANTIC"

    # Default to SQL if unsure (safer for legal DB)
    return "SQL"

def generate_semantic_answer(query: str, results: list) -> str:
    """Generate RAG answer for semantic/situation results."""
    if not results:
        return "No similar cases found for this situation."
        
    context_text = ""
    for i, res in enumerate(results, 1):
        context_text += f"\nCASE {i}:\n"
        context_text += f"Case No: {res.get('case_no')}\n"
        context_text += f"Subject: {res.get('subject')}\n"
        # Since we don't have full judgment in limited vector result, we rely on what we have.
        # In a real app, we'd fetch the full judgment content by ID here if needed.
        # For now, let's assume the vector metadata/doc content is sufficient for a summary.
        doc_snippet = res.get('subject') # Or fetch from DB if needed? 
        # Actually, let's fetch the summary/judgment snippet from DB for these IDs to give AI good context
        
    # Upgrade: Fetch better context for the found IDs
    case_ids = [str(r['id']) for r in results if 'id' in r]
    if case_ids:
        try:
            conn = get_db_connection()
            if conn:
                cursor = conn.cursor(dictionary=True)
                # fetch judgment snippet
                format_strings = ','.join(['%s'] * len(case_ids))
                cursor.execute(f"SELECT id, case_no, judgment, subject FROM ocr_extractions WHERE id IN ({format_strings})", tuple(case_ids))
                db_rows = cursor.fetchall()
                cursor.close()
                conn.close()
                
                # Map back to results
                db_map = {str(row['id']): row for row in db_rows}
                context_text = "" # Reset
                for i, res in enumerate(results, 1):
                    cid = str(res.get('id'))
                    if cid in db_map:
                        row = db_map[cid]
                        judgment_snip = row['judgment'][:1500] if row['judgment'] else "N/A"
                        context_text += f"\n--- CASE {i} ---\n"
                        context_text += f"Case No: {row['case_no']}\n"
                        context_text += f"Subject: {row['subject']}\n"
                        context_text += f"Excerpt: {judgment_snip}...\n"
                        # Add link for AI
                        link = f"http://127.0.0.1:8000/subscriber/singleDecision/{cid}"
                        context_text += f"Link: {link}\n"
        except Exception as e:
            logger.error(f"Error fetching context for semantic search: {e}")

    prompt = f"""User Description: "{query}"

Here are legal cases that might be relevant:
{context_text}

Task:
1. Analyze the User's Situation.
2. For EACH case provided, explain clearly **HOW it relates** to the user's situation. Does it match the crime? The circumstances? The legal principle?
3. If a case is NOT relevant, say "Less relevant".
4. Provide a disclaimer that this is AI research, not legal advice.

Format:
- **Case No:** [Case No]
- **Relevance:** [Explanation]
- [🔗 View Full Case](Link)
"""
    return call_gemini_api(prompt, max_tokens=800)


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
- judge_name: Name of the judge (short)
- judges: Full names of all judges (search here too)
- content: Full text content
- parties: Names of parties. IMPORTANT: content may contain HTML (e.g., `&amp;`, `&nbsp;`, `<strong>`). regex and wildcards are your friends. for "A & B", use `LIKE '%A%B%'`
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
- homepage: Homepage flag
- deleted_at: Deletion timestamp (NULL if active){context_info}

IMPORTANT RULES:
1. Return ONLY SQL query, no explanation or markdown
2. Use LIKE '%keyword%' for text searches (case-insensitive)
3. For case numbers, search in case_no field
4. For judges, search in BOTH judge_name AND judges columns using OR
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
16. ALWAYS INCLUDE the following columns in your SELECT statement: 'id', 'case_no', 'parties', 'result', 'decided_on', 'judges', 'judge_name', 'jurisdiction', 'book_volume', 'published_year', 'starting_page_no', 'ending_page_no', 'related_act_order_rule', 'sections_subsections', 'petitioners', 'respondent', 'judgment'
17. CORRECT TYPOS: If the user makes a spelling mistake (e.g., "cmmissioner", "incom-tax"), use the CORRECTED spelling (e.g., "commissioner", "income-tax") in your SQL LIKE clauses. Use your knowledge of legal terms and proper names to fix errors.
18. CONVERT DATES: Transform natural language dates like "17th March, 1982" into 'YYYY-MM-DD' format (e.g., '1982-03-17') for the `decided_on` column.
19. LOGICAL OPERATORS: When a user specifies multiple conditions (e.g., "Judge X AND Date Y"), use the `AND` operator to strictly require both.
20. CLEAN NAMES: When searching for Judges or Parties, REMOVE titles like 'C.J.', 'Justice', 'Mr.', 'Mrs.', 'Dr.', 'Advocate' and punctuation. Only search for the core name (e.g., "Kemaluddin Hossain C.J," -> `judge_name LIKE '%Kemaluddin Hossain%'`).
21. SUMMARY/DETAILS REQUESTS: If user asks for "summary", "gist", "brief", "details", "tell me about", or "what happened", you MUST include the `judgment` column in your SELECT statement. This is CRITICAL for generating summaries.
22. CONTEXT RETENTION: IF a case context is set (e.g., "[CONTEXT: Current case is X]") AND the user query is not explicitly a new search (i.e. does not contain "find cases", "search for", "show me cases"), YOU MUST restrict your SQL to that case using `WHERE case_no LIKE '%X%'`. Treat "which page?", "what date?", "who is judge?" as follow-ups about the CURRENT case.
23. ARTICLE/SECTION SEARCHES: If user asks for "Article X of Act Y", you MUST SPLIT the search. Search `related_act_order_rule LIKE '%Y%'` AND `sections_subsections LIKE '%X%'`. Do NOT search for the full phrase "Article X of Act Y" in a single column. Note that sections often contain dashes (e.g. "Article—102"), so use broad wildcards like separate `LIKE` clauses or just the number.
24. CITATION/SECTION REQUESTS: If user asks "what section?", "find rules", "articles mentioned", or "legal basis", you MUST include the `judgment` column in your SELECT statement to allow scanning the full text.
25. PAGE LOOKUP: If user asks for "Page X of Volume Y", you MUST search for the case that CONTAINS that page. Use `book_volume = Y AND starting_page_no <= X AND ending_page_no >= X`. You MUST SELECT: `id, case_no, parties, book_volume, starting_page_no, ending_page_no, published_year, judge_name, decided_on, result, subject, judgment, petitioners, respondent`. Do NOT search for strict equality `starting_page_no = X`.
26. VOLUME SEARCH: `book_volume` is often NULL. To find "Volume X", you MUST calculate the year: `Year = 1980 + X`. Your search condition MUST be: `(book_volume = X OR published_year = [CalculatedYear])`. Example: 'Volume 3' -> `(book_volume = 3 OR published_year = 1983)`.
27. HTML/AMPERSAND HANDLING: If user query contains "&" (e.g. "Latif & Another"), you MUST use wildcards. Do NOT search `LIKE '%Latif & Another%'`. Search `LIKE '%Latif%Another%'`. The DB contains `&amp;` so exact `&` match fails.
28. DELETED CASES: You must ALWAYS exclude deleted cases. Add `deleted_at IS NULL` to every query's WHERE clause. Example: `WHERE case_no LIKE '%...' AND deleted_at IS NULL`.

QUERY EXAMPLES:

Q: "Tell me about case 582 of 2001"
SQL: SELECT id, case_no, parties, division, decided_on, judge_name, subject, result FROM ocr_extractions WHERE case_no LIKE '%582%' AND case_no LIKE '%2001%' LIMIT 5;

Q: "What case is mentioned in page 140 of volume 59?"
SQL: SELECT id, case_no, parties, book_volume, starting_page_no, ending_page_no, published_year FROM ocr_extractions WHERE (book_volume = 59 OR published_year = 2039) AND starting_page_no <= 140 AND ending_page_no >= 140 LIMIT 5;

Q: "Find Civil Revision No. 6085 of 2007"
SQL: SELECT id, case_no, parties, division, decided_on, judge_name, subject, result, petitioners, respondent FROM ocr_extractions WHERE case_no LIKE '%6085%' AND case_no LIKE '%2007%' LIMIT 5;

Q: "Find Civil Revision No. 6085 of 2007"
SQL: SELECT id, case_no, parties, division, decided_on, judge_name, subject, result, petitioners, respondent FROM ocr_extractions WHERE case_no LIKE '%6085%' AND case_no LIKE '%2007%' LIMIT 5;

Q: "Who was the judge in this case?" (with context: case 582)
SQL: SELECT id, case_no, judge_name, decided_on FROM ocr_extractions WHERE case_no LIKE '%582%' LIMIT 5;

Q: "What sections were mentioned?"
SQL: SELECT id, case_no, sections_subsections, related_act_order_rule, judgment FROM ocr_extractions WHERE case_no LIKE '%{st.session_state.current_case if st.session_state.current_case else ''}%' LIMIT 5;

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

Q: "Find Death Reference No. 6 of 2001"
SQL: SELECT case_no, parties, decided_on, subject, result, judgment FROM ocr_extractions WHERE case_no LIKE '%Death Reference%' AND case_no LIKE '%6%' AND case_no LIKE '%2001%' LIMIT 5;

Q: "Cases involving Article 102 of the Constitution"
SQL: SELECT case_no, parties, related_act_order_rule, sections_subsections, subject FROM ocr_extractions WHERE (related_act_order_rule LIKE '%Constitution%' AND sections_subsections LIKE '%102%') OR content LIKE '%Article 102%' LIMIT 5;

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

Q: "Summarize case 582 of 2001"
SQL: SELECT case_no, parties, subject, result, judgment FROM ocr_extractions WHERE case_no LIKE '%582%' AND case_no LIKE '%2001%' LIMIT 1;


Question: {question}
SQL:"""
    
    # PREPARE PROMPT WITH HINTS
    # We inject hints directly into the question block to ensure maximizing attention
    prompt_hints = ""
    
    # 0. Ampersand Hint
    if "&" in question or " and " in question.lower():
        prompt_hints += "\n[SYSTEM HINT: Query contains '&' or 'and'. Database often uses HTML '&amp;'. USE WILDCARDS `%` instead of specific joining characters in LIKE clauses.]"

    
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
        prompt_hints += f"\n[CONTEXT: Current case is {st.session_state.current_case}. User likely asks about THIS case. Restrict SQL to `case_no LIKE '%{st.session_state.current_case}%'` unless asked to search new.]"
    
    # Constitution/Act Hint
    if "constitution" in question.lower() and "article" in question.lower():
        prompt_hints += "\n[SYSTEM HINT: User is asking about Constitution Article. YOU MUST SPLIT SEARCH: `related_act_order_rule LIKE '%Constitution%' AND sections_subsections LIKE '%[Number]%'`. Do NOT search for 'Article X of Constitution' as one string.]"
    if st.session_state.current_parties:
        prompt_hints += f"\n[CONTEXT: Current parties are {st.session_state.current_parties}]"
    
    # PROMPT AUGMENTATION: Force context into the question
    final_question = question
    if st.session_state.current_case and "search" not in question.lower() and "find" not in question.lower():
        final_question = f"[Context: Regarding {st.session_state.current_case}] {question}"

    final_prompt = f"Question: {final_question}{prompt_hints}\n\n{schema_info}"

    
    try:
        sql_response = call_gemini_api(final_prompt, max_tokens=200)
        if not sql_response:
             logger.error("Empty response from API")
             return None
             
        st.session_state.api_calls += 1
        
        sql = sql_response.strip()
        sql = sql.replace('```sql', '').replace('```', '').strip()

        # SQL ENFORCER: Programmatically force context if users asks follow-up
        if st.session_state.current_case and "search" not in final_question.lower() and "find" not in final_question.lower():
             # If the AI generated a wildcard or broad search, we clamp it down
             if st.session_state.current_case not in sql:
                 logger.info(f"SQL ENFORCER: Injecting context {st.session_state.current_case} into SQL")
                 if "WHERE" in sql.upper():
                     sql = re.sub(r'WHERE', f"WHERE case_no LIKE '%{st.session_state.current_case}%' AND ", sql, count=1, flags=re.IGNORECASE)
                 else:
                     # Add WHERE clause if missing (unlikely but safe)
                     if "LIMIT" in sql.upper():
                         sql = re.sub(r'LIMIT', f"WHERE case_no LIKE '%{st.session_state.current_case}%' LIMIT", sql, count=1, flags=re.IGNORECASE)
                     else:
                         sql += f" WHERE case_no LIKE '%{st.session_state.current_case}%'"

    
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
                answer_text = call_gemini_api(prompt, max_tokens=150)
                if not answer_text:
                     return "Could not find answer in available cases."
                     
                st.session_state.api_calls += 1
                return f"📄 **From Judgment Search:**\n\n{answer_text.strip()}\n\n*Case: {case_no}*"
            except Exception as e:
                logger.error(f"Fallback answer error: {e}")
                return "Could not find answer in available cases."
        else:
            return "No cases found matching your query. Try rephrasing or ask about a different case."
    
    # Update context if we found a case (Context Locking)
    # Only update if no context exists OR user explicitly requested a new search
    update_context = False
    if results and len(results) > 0:
         if not st.session_state.current_case:
             update_context = True # First search
         elif "search" in question.lower() or "find" in question.lower() or "show" in question.lower():
             update_context = True # Explicit new search
             
    if update_context and results:
        first_result = results[0]
        if 'case_no' in first_result and first_result['case_no']:
            st.session_state.current_case = first_result['case_no']
        if 'id' in first_result:
            st.session_state.current_case_id = first_result['id']
        if 'parties' in first_result and first_result['parties']:
            st.session_state.current_parties = first_result['parties']
    
    # Calculate Volume based on Year (Volume = Year - 1980)
    for result in results:
        try:
            if 'published_year' in result and result['published_year']:
                year = int(result['published_year'])
                if year > 1980:
                    result['book_volume'] = year - 1980
        except (ValueError, TypeError):
            pass

    

    # Format results for AI (token-efficient)
    results_text = ""
    # Smart context management: If many results, strip heavy text fields to avoid token errors
    # BUT if user asks for details/summary, we MUST provide judgment text (truncated if needed)
    force_detail = any(k in question.lower() for k in ["detail", "summary", "gist", "brief", "tell me about", "facts", "arguments", "decision"])
    include_full_text = len(results) <= 3 or force_detail
    
    for i, result in enumerate(results, 1):  # Process ALL results
        results_text += f"\n--- Case {i} ---\n"
        for key, value in result.items():
            # Skip massive text fields if we have many results, unless specifically needed
            # Skip massive text fields if we have many results, unless specifically needed
            if not include_full_text and key in ['content', 'judgment', 'file_path']:
                # Even if skipping full text, include a snippet of judgment for context
                 if key == 'judgment' and value:
                     value = value[:4000] + "... [Truncated]"
                     results_text += f"{key}: {value}\n"
                 continue
                
            if value and key in ['case_no', 'parties', 'petitioners', 'respondent', 
                                 'division', 'decided_on', 'judge_name', 'judges', 'subject', 'result',
                                 'sections_subsections', 'related_act_order_rule', 'volume_id', 'book_volume', 'published_year',
                                 'published_month', 'starting_page_no', 'ending_page_no', 'key_words', 'jurisdiction', 
                                 'content', 'judgment', 'file_name', 'status', 'id', 'homepage']:
                # Do not truncate - User requested full text
                # if isinstance(value, str) and len(value) > 300:
                #     value = value[:300] + "..."
                results_text += f"{key}: {value}\n"
        
        # INJECT LINK FOR EVERY CASE so AI can display it
        if 'id' in result:
             link = f"http://127.0.0.1:8000/subscriber/singleDecision/{result['id']}"
             results_text += f"View Full Case Link: {link}\n"

    
    # Answer prompt
    prompt = f"""Answer the question based on the database results.
    
IMPORTANT FORMATTING:
- **PAGE FORMAT**: Combine starting and ending pages into a single line: **Page:** [start] to [end] (e.g. **Page:** 23 to 25).
- **CLEAN DATA**: Remove ALL HTML tags (e.g., <strong>, <br>, <p>, <em>, <span>, <div>). The output must be plain markdown text only.
- **BULLET POINTS**: You MUST use a bulleted list format for case details. Do NOT put them on one line.

ANSWERING RULES:
1. **SPECIFIC QUESTIONS** (e.g., "Who is the judge?", "What is the date?"):
   - ANSWER ONLY THE SPECIFIC QUESTION.
   - Do NOT list Case No, Volume, Year, or Parties unless explicitly asked.
   - Example Q: "Who is the judge?" -> A: "The judge was **Justice Mr. X**." (That's it).

2. **GENERAL SEARCH / DETAILS** (e.g., "Tell me about case X", "Find cases about land"):
   - Provide a structured summary.
   - **MANDATORY OUTPUT FORMAT**:
     *   **Case No:** [Case No]
     *   **Volume:** [Book Volume]
     *   **Year:** [Published Year]
     *   **Page:** [Start] to [End]
     *   **Decided On:** [Date]
     *   **Result:** [Result]
     *   **Parties:** [Parties]
     *   **Hon'ble Judge(s):** [Judge Names]
     *   **For the Appellants:** [Value from 'petitioners' column]
     *   **For the Respondent:** [Value from 'respondent' column]
     *   **Summery of the Subject Matter:** [Subject - YOU MUST Summarize this to maximum 4 lines if the text is longer]
     *   **Jurisdiction:** [Jurisdiction]
     *   **Related Acts/Rules/Orders:** [Acts/Rules]
     *   **Summery of JUDGMENT:**
         *   **Facts:** [Brief facts]
         *   **Arguments:** [Brief arguments]
         *   **Decision:** [Court's decision]
     *   [🔗 **View Full Case**](Link from data)

   - **SEPARATOR**: You MUST put a horizontal line `---` after each case to separate them.
   - **MANDATORY**: Use "Not Found" if a field is empty (except for Summary, generate that).
   - **MANDATORY**: You MUST include the [🔗 View Full Case](Link) for EACH case you list. Use the 'View Full Case Link' provided in the data.
   - Bold the field labels (e.g., **Volume:**).

3. **SUMMARIZATION** (e.g. "Give me the summary", "What is the gist"):
   - Ignore the "MANDATORY" metadata rule.
   - Focus purely on summarizing the `judgment` text if available.
   - **Structure**:
     * **Facts:** [Detailed account of facts]
     * **Arguments:** [Detailed summary of arguments from both sides]
     * **Decision:** [Comprehensive explanation of judgment]
   - **LENGTH**: The summary MUST be broad and comprehensive (approx 150-200 words).
   - **CUSTOM LENGTH**: IF the user specifies a length (e.g. "400 words", "detailed", "long"), you MUST respect that constraint and expand the summary accordingly.

4. **SECTION LIST / CITATIONS** (e.g. "List sections", "sections mentioned"):
   - Scan the `judgment` text and `related_act_order_rule` / `sections_subsections` columns.
   - **Structure**:
     * **Acts/Rules Cited:** [List all Acts]
     * **Specific Sections:**
       - [Act Name] Section [X]
       - [Act Name] Section [Y]
   - **MANDATORY**: Ensure you catch ALL sections mentioned in the text.

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
            
        answer_text = call_gemini_api(prompt, max_tokens=2000)
        if not answer_text:
             return f"Found {len(results)} case(s) but couldn't generate answer."
             
        st.session_state.api_calls += 1
        
        # Append Source Link
        if st.session_state.current_case_id:
             # MUST use absolute URL to point to Laravel port 8000, not Streamlit 8501
             answer_text += f"\n\n[🔗 **View Full Case**](http://127.0.0.1:8000/subscriber/singleDecision/{st.session_state.current_case_id})"

        return answer_text.strip()
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

        /* ============================================ */
        /* SIDEBAR STYLING (TEXT ONLY LINKS) */
        /* ============================================ */
        
        /* Reset all sidebar buttons to look like plain text */
        [data-testid="stSidebar"] .stButton > button {{
            width: 100%;
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            text-align: left !important;
            padding: 2px 0px !important; /* Minimal padding */
            margin: 2px 0 !important;
            color: #444 !important;
            font-weight: normal !important;
            height: auto !important;
            min-height: auto !important;
            line-height: 1.5 !important;
            display: block !important;
            border-radius: 0 !important;
        }}

        /* Hover: Only Text Color Change or Underline */
        [data-testid="stSidebar"] .stButton > button:hover {{
            color: #000 !important;
            text-decoration: underline !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }}

        /* Focus/Active/Pressed: No Box */
        [data-testid="stSidebar"] .stButton > button:focus,
        [data-testid="stSidebar"] .stButton > button:active {{
            color: #000 !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
        }}
        


        /* Specific: Style TOP ROW Buttons (New Chat, Toggle, Clear) - UNIFIED STYLE */
        /* All 3 buttons in the horizontal block will look identical: Blue, Rounded, Equal Size */
        [data-testid="stSidebar"] [data-testid="stHorizontalBlock"] button {{
            background-color: #dbeafe !important;
            color: #1e40af !important;
            border-radius: 12px !important;
            border: none !important;
            padding: 0 !important; /* Let flexbox center content */
            margin: 0 !important;
            text-align: center !important;
            width: 100% !important;
            height: 42px !important;
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
            font-weight: 600 !important;
            box-shadow: none !important;
            text-decoration: none !important;
            transition: all 0.2s ease;
            font-size: 1rem !important; /* Unify font size */
        }}

        [data-testid="stSidebar"] [data-testid="stHorizontalBlock"] button:hover {{
            background-color: #bfdbfe !important;
            color: #1e3a8a !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
        }}

    </style>
    """, unsafe_allow_html=True)

apply_custom_css()

# Sidebar
with st.sidebar:
    # History Section
    if st.session_state.user_id:
        # TOP ROW: New Chat + Actions (3 Equal Columns)
        c1, c2, c3 = st.columns(3)
        
        with c1:
            # Shortened label to fit in 1/3 width
            if st.button("📝", key="new_chat_main", help="New Chat", use_container_width=True):
                st.session_state.messages = []
                st.session_state.current_session_id = str(uuid.uuid4())
                st.session_state.current_case = None 
                st.rerun()
                
        with c2:
            # Toggle Sources Icon
            icon_s = "📖" if st.session_state.show_sources else "📕"
            if st.button(icon_s, key="toggle_sources_side", help="Toggle Sources", use_container_width=True):
                st.session_state.show_sources = not st.session_state.show_sources
                st.rerun()
                
        with c3:
            # Clear Chat Icon
            if st.button("🧹", key="clear_chat_side", help="Clear Conversation", use_container_width=True):
                 st.session_state.messages = []
                 if not st.session_state.context_locked:
                     st.session_state.current_case = None
                 st.rerun()
            
        # Spacer
        st.markdown("<div style='margin-bottom: 20px;'></div>", unsafe_allow_html=True)
        
        # LOAD SESSIONS LIST
        try:
            conn = get_db_connection()
            if conn:
                cursor = conn.cursor(dictionary=True)
                query = f"""
                SELECT session_id, 
                        MIN(created_at) as started_at,
                        (SELECT content FROM ai_chat_histories h2 WHERE h2.session_id = h1.session_id AND role='user' ORDER BY id ASC LIMIT 1) as title
                FROM ai_chat_histories h1 
                WHERE user_id = {st.session_state.user_id} 
                GROUP BY session_id 
                ORDER BY started_at DESC 
                LIMIT 15
                """
                cursor.execute(query)
                sessions = cursor.fetchall()
                
                # Tiny Header "Chats"
                st.markdown("<p style='font-size: 12px; font-weight: 600; color: #666; margin-bottom: 5px; margin-top: 10px;'>Chats</p>", unsafe_allow_html=True)
                
                for sess in sessions:
                    sid = sess['session_id']
                    if not sid: continue 
                    
                    title = sess['title'] if sess['title'] else "New Chat"
                    if len(title) > 28: title = title[:28] + "..."
                    
                    # No icon in the list items (as per image reference), just text
                    if st.button(f"{title}", key=sid, use_container_width=True):
                        st.session_state.current_session_id = sid
                        st.session_state.messages = []
                        cursor.execute(f"SELECT role, content FROM ai_chat_histories WHERE session_id = '{sid}' ORDER BY id ASC")
                        history = cursor.fetchall()
                        for msg in history:
                            st.session_state.messages.append({"role": msg['role'], "content": msg['content']})
                        st.rerun()

                cursor.close()
                conn.close()
        except Exception as e:
            st.error(f"Error loading history: {e}")
            
        # Clear All History (Pinned to Bottom)
        st.markdown(
            """
            <style>
                .sidebar-footer {
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    width: 280px; /* Match sidebar width */
                    padding: 1rem;
                    background: inherit; /* Blend with sidebar */
                    z-index: 999;
                    border-top: 1px solid rgba(0,0,0,0.1);
                }
                
                /* Adjust main sidebar content to not be covered */
                [data-testid="stSidebar"] > div:first-child {
                    padding-bottom: 80px;
                }
            </style>
            <div class="sidebar-footer">
            """,
            unsafe_allow_html=True
        )
        
        if st.button("🗑️ Delete All History", use_container_width=True):
            try:
                conn = get_db_connection()
                if conn:
                    cursor = conn.cursor()
                    cursor.execute(f"DELETE FROM ai_chat_histories WHERE user_id = {st.session_state.user_id}")
                    conn.commit()
                    cursor.close()
                    conn.close()
                    st.session_state.messages = []
                    st.rerun()
            except Exception as e:
                st.error(f"Failed: {e}")
        
        st.markdown("</div>", unsafe_allow_html=True)
    else:
        st.warning("⚠️ No User ID detected. History not saving.")
        st.caption("Access via the BLD Dashboard to enable history.")

# Top Control Bar - REMOVED (Moved to Sidebar)
with st.container():
    # Only show context lock banner if needed
    if st.session_state.context_locked and st.session_state.current_case:
         st.info(f"🔒 **Context Locked**: Discussing **{st.session_state.current_case}**")
    
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



# Check User Module Access (Dynamic Feature Module)
def check_user_module_access(user_id, module_slug='ai-research'):
    """Check if user has access to a specific feature module via their package"""
    if not user_id:
        return False
        
    try:
        conn = get_db_connection()
        if not conn:
            return False
            
        cursor = conn.cursor(dictionary=True)
        # Complex Join: Subscription -> Package -> Relation -> Feature -> Module
        query = """
            SELECT 1
            FROM subscriptions s
            JOIN packages p ON s.package_id = p.id
            JOIN package_feature_relations pfr ON p.id = pfr.package_id
            JOIN package_features pf ON pfr.feature_id = pf.id
            JOIN package_feature_modules pfm ON pf.id = pfm.feature_id
            WHERE s.subscriber_id = %s
            AND s.status = 1 
            AND (s.expire_date >= CURDATE() OR s.expire_date IS NULL)
            AND pfm.slug = %s
            LIMIT 1
        """
        cursor.execute(query, (user_id, module_slug))
        result = cursor.fetchone()
        cursor.close()
        conn.close()
        
        return result is not None
    except Exception as e:
        logger.error(f"Module access check error: {e}")
        return False

# Chat input (Global Bottom)
if prompt := st.chat_input("Ask about a legal case..."):
    
    # 0. DYNAMIC ACCESS CONTROL CHECK
    if st.session_state.user_id:
        # Check if user has the 'ai-research' module enabled in their package
        has_access = check_user_module_access(st.session_state.user_id, 'ai-research')
        
        if not has_access:
             warning = "🚫 **Access Denied**: Your current plan does not include the **AI Research** feature. Please upgrade your package to access this tool."
             st.chat_message("assistant").error(warning)
             st.stop()
             
    # Pre-process: Handle "Case 1", "Case 2" selection from previous results
    processed_prompt = prompt
    
    # 1. Handle Selection (Case 1, Case 2...)
    match_selection = re.search(r'\b(?:case|result|number)\s+(\d+)', prompt, re.IGNORECASE)
    if match_selection and st.session_state.latest_results:
        try:
            idx = int(match_selection.group(1)) - 1
            if 0 <= idx < len(st.session_state.latest_results):
                selected_case = st.session_state.latest_results[idx]
                case_no = selected_case.get('case_no')
                if case_no:
                    st.info(f"Selecting Case {idx+1}: {case_no}")
                    processed_prompt = f"Give me details for case {case_no}. Original Request: {prompt}"
                    st.session_state.current_case = case_no
        except Exception as e:
            logger.error(f"Selection error: {e}")



    # Add user message
    st.session_state.messages.append({"role": "user", "content": prompt})
    
    # SAVE USER MESSAGE
    if st.session_state.user_id:
        save_chat_message(st.session_state.user_id, "user", prompt, st.session_state.current_session_id)

    with st.chat_message("user"):
        st.markdown(prompt)
    
                # Generate Response based on Intent
    with st.spinner("Analyzing legal precedents..."):
        
        # 0. Context Check (Unlock if switching cases)
        check_and_unlock_context(processed_prompt)

        # 1. Classify Intent
        intent = classify_query_intent(processed_prompt)
        logger.info(f"Query Intent: {intent}")
        
        response_text = ""
        results = []
        
        if intent == "SEMANTIC":
            # Semantic Path
            results = perform_semantic_search(processed_prompt, limit=5)
            if results:
                response_text = generate_semantic_answer(processed_prompt, results)
            else:
                response_text = "No cases found matching that situation description."
                
        else:
            # SQL Path (Legacy Logic)
            sql = generate_sql(processed_prompt)
            if sql:
                results = execute_sql(sql)
                if results:
                    response_text = generate_answer(processed_prompt, results)
                else:
                     # Fallback to judgment search
                     response_text = generate_answer(processed_prompt, [], used_fallback=True)
            else:
                 response_text = "Sorry, I couldn't understand how to search for that. Please try rephrasing."

        # 2. Save Results for UI Sidebar
        if results:
            st.session_state.latest_results = results
            
        # 3. Cleanup response text
        if response_text:
            response_text = response_text.replace("```markdown", "").replace("```", "")
            
        # 4. Display Assistant Message
        st.session_state.messages.append({"role": "assistant", "content": response_text})
        st.chat_message("assistant").write(response_text)
        
        # 5. Save to History
        if st.session_state.user_id:
             save_chat_message(st.session_state.user_id, "assistant", response_text, st.session_state.current_session_id)
        
        # 6. Rerun to update Sidebar
        if results:
            st.rerun()


# Footer

