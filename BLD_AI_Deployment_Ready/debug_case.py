
import mysql.connector
import json

def get_case(case_id):
    config = {
        'host': 'localhost',
        'port': 3306,
        'database': 'bldlegalized_bld',
        'user': 'root', 
        'password': '', # CORRECTED: Empty password for localhost
        'charset': 'utf8mb4'
    }
    
    try:
        conn = mysql.connector.connect(**config)
        cursor = conn.cursor(dictionary=True)
        cursor.execute(f"SELECT id, parties, book_volume, published_year, case_no, petitioners, respondent FROM ocr_extractions WHERE id = {case_id}")
        result = cursor.fetchone()
        print(json.dumps(result, indent=2, default=str))
        conn.close()
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    get_case(3843)
