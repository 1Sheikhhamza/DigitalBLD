import chromadb
from chromadb.utils import embedding_functions
import os
from pypdf import PdfReader
from tqdm import tqdm
import logging

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

# Initialize ChromaDB Client (Same path as main app)
chroma_client = chromadb.PersistentClient(path="./chroma_db")

# Use default embeddings
embedding_func = embedding_functions.DefaultEmbeddingFunction()

# Create or Get 'knowledge_base' Collection
collection = chroma_client.get_or_create_collection(
    name="bld_knowledge_base",
    embedding_function=embedding_func,
    metadata={"hnsw:space": "cosine"}
)

PDF_DIR = "./knowledge_base_pdfs"

def ingest_pdfs():
    """Scan PDF folder and ingest into ChromaDB"""
    if not os.path.exists(PDF_DIR):
        logger.error(f"Directory {PDF_DIR} not found. Please create it and add PDFs.")
        return

    files = [f for f in os.listdir(PDF_DIR) if f.lower().endswith('.pdf')]
    if not files:
        logger.warning(f"No PDF files found in {PDF_DIR}.")
        return

    logger.info(f"Found {len(files)} PDFs. Starting ingestion...")

    ids = []
    documents = []
    metadatas = []
    
    chunk_size = 1000 # characters
    overlap = 100

    for filename in files:
        file_path = os.path.join(PDF_DIR, filename)
        logger.info(f"Processing {filename}...")
        
        try:
            reader = PdfReader(file_path)
            full_text = ""
            for page in reader.pages:
                text = page.extract_text()
                if text:
                    full_text += text + "\n"
            
            # Simple chunking logic
            if not full_text:
                logger.warning(f"Could not extract text from {filename}")
                continue

            # Split text into chunks
            for i in range(0, len(full_text), chunk_size - overlap):
                chunk = full_text[i:i + chunk_size]
                if len(chunk) < 50: continue # Skip tiny chunks
                
                chunk_id = f"{filename}_chunk_{i}"
                
                ids.append(chunk_id)
                documents.append(chunk)
                metadatas.append({
                    "source": filename,
                    "type": "act_rule"
                })

                # Batch upsert
                if len(ids) >= 50:
                    collection.upsert(ids=ids, documents=documents, metadatas=metadatas)
                    ids = []
                    documents = []
                    metadatas = []
                    
        except Exception as e:
            logger.error(f"Failed to process {filename}: {e}")

    # Final batch
    if ids:
        collection.upsert(ids=ids, documents=documents, metadatas=metadatas)

    logger.info("PDF Ingestion Complete!")

if __name__ == "__main__":
    ingest_pdfs()
