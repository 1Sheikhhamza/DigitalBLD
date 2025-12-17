import chromadb
from chromadb.utils import embedding_functions
import mysql.connector
from mysql.connector import Error
import os
from dotenv import load_dotenv
from tqdm import tqdm
import logging
import time

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

# Load environment variables
load_dotenv()

# Initialize ChromaDB Client
# PersistentClient saves data to disk at 'chroma_db' folder
chroma_client = chromadb.PersistentClient(path="./chroma_db")

# Use Google Generative AI Embeddings? (Optional, helps but requires API key)
# For now, let's use the default SentenceTransformer (all-MiniLM-L6-v2) which is built-in/downloaded auto
# by Chroma if no embedding function is provided.
# However, to be explicit and robust, we can use the default ef.
embedding_func = embedding_functions.DefaultEmbeddingFunction()

# Create or Get Collection
collection = chroma_client.get_or_create_collection(
    name="bld_cases",
    embedding_function=embedding_func,
    metadata={"hnsw:space": "cosine"} # Cosine similarity for semantic search
)

def get_db_connection():
    """Connect to MySQL database"""
    try:
        conn = mysql.connector.connect(
            host=os.getenv("DB_HOST", "localhost"),
            user=os.getenv("DB_USER", "root"),
            password=os.getenv("DB_PASSWORD", ""),
            database=os.getenv("DB_NAME", "bldlegalized_bld")
        )
        if conn.is_connected():
            return conn
    except Error as e:
        logger.error(f"Error connecting to MySQL: {e}")
    return None

def ingest_data(limit=None):
    """Fetch data from MySQL and ingest into ChromaDB"""
    conn = get_db_connection()
    if not conn:
        return

    cursor = conn.cursor(dictionary=True)
    
    # Query to fetch relevant text data
    # We concatenate meaningful fields for the 'document' embedding
    query = """
        SELECT id, case_no, parties, judge_name, decided_on, subject, content, judgment, published_year
        FROM ocr_extractions
        WHERE content IS NOT NULL OR judgment IS NOT NULL
    """
    if limit:
        query += f" LIMIT {limit}"
    
    logger.info("Fetching data from MySQL...")
    cursor.execute(query)
    results = cursor.fetchall()
    logger.info(f"Fetched {len(results)} records.")
    
    ids = []
    documents = []
    metadatas = []
    
    logger.info("Processing and generating embeddings (this may take time)...")
    
    for row in tqdm(results):
        case_id = str(row['id'])
        
        # Construct the text to embed (Semantic Representation)
        # We combine Subject + Headnotes + Summary/Content to capture the "Meaning"
        # If 'subject' is empty, use start of content
        text_content = ""
        if row['subject']:
            text_content += f"Subject: {row['subject']}\n"
        
        # Add summary or snippet of judgment
        # Limitation: Models typically handle ~512-8192 tokens. 
        # For simplicity in this v1, we take the first 2000 chars which usually contains the issue/summary.
        raw_text = row['content'] if row['content'] else row['judgment']
        if raw_text:
            text_content += f"Content: {raw_text[:2000]}"
            
        if not text_content.strip():
            continue
            
        ids.append(case_id)
        documents.append(text_content)
        
        # Metadata for filtering/retrieval
        metadatas.append({
            "case_no": row['case_no'] or "N/A",
            "parties": row['parties'] or "N/A",
            "year": str(row['published_year']) if row['published_year'] else "N/A",
            "judge": row['judge_name'] or "N/A"
        })
        
        # Batch ingest to prevent memory issues (e.g. every 100 items)
        if len(ids) >= 100:
            collection.upsert(ids=ids, documents=documents, metadatas=metadatas)
            ids = []
            documents = []
            metadatas = []

    # Final batch
    if ids:
        collection.upsert(ids=ids, documents=documents, metadatas=metadatas)
        
    logger.info("Ingestion complete!")
    cursor.close()
    conn.close()

if __name__ == "__main__":
    # Test run with limit first, then remove limit for full ingest
    # Set limit=100 for testing, None for full
    print("Starting Ingestion...")
    ingest_data(limit=200) 
