
import chromadb
import logging

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

def test_search():
    try:
        print("Initializing ChromaDB Client...")
        client = chromadb.PersistentClient(path="./chroma_db")
        print("Client initialized. Getting collection...")
        collection = client.get_collection(name="bld_cases")
        print(f"Collection found. Item count: {collection.count()}")
        
        query = "person jump over a wall and back with some gold iteam"
        print(f"Querying: '{query}'")
        
        results = collection.query(
            query_texts=[query],
            n_results=5
        )
        
        print("Results:")
        print(results)
        
    except Exception as e:
        print(f"ERROR: {e}")

if __name__ == "__main__":
    test_search()
