import mysql.connector
import os
from dotenv import load_dotenv

load_dotenv()

def debug():
    try:
        config = {
            'host': 'localhost',
            'port': 3306,
            'database': 'bldlegalized_bld',
            'user': 'root',
            'password': '',
            'charset': 'utf8mb4',
            'use_unicode': True
        }
        conn = mysql.connector.connect(**config)
        cursor = conn.cursor(dictionary=True)
        
        print("--- Columns in ocr_extractions ---")
        cursor.execute("DESCRIBE ocr_extractions")
        columns = cursor.fetchall()
        for col in columns:
            print(f"{col['Field']} ({col['Type']})")
            
        conn.close()
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    debug()
