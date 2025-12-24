import mysql.connector
import os
import time
from dotenv import load_dotenv

load_dotenv()

def get_db_connection():
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
        return conn
    except Exception as e:
        print(f"Connection error: {e}")
        return None

def fix_duplicates():
    conn = get_db_connection()
    if not conn:
        return

    cursor = conn.cursor(dictionary=True)
    slug_to_fix = 'ai-research'
    
    tables = ['package_features', 'package_feature_modules']
    
    for table in tables:
        print(f"Checking table: {table}")
        # Find conflicting entry (even if deleted)
        query = f"SELECT id, slug, deleted_at FROM {table} WHERE slug = %s"
        cursor.execute(query, (slug_to_fix,))
        rows = cursor.fetchall()
        
        for row in rows:
            print(f"Found conflict in {table} ID: {row['id']} (Deleted: {row['deleted_at']})")
            
            # Generate new slug
            new_slug = f"{slug_to_fix}-old-{int(time.time())}"
            print(f"Renaming slug to: {new_slug}")
            
            update_query = f"UPDATE {table} SET slug = %s WHERE id = %s"
            cursor.execute(update_query, (new_slug, row['id']))
            conn.commit()
            print("Successfully renamed.")

    conn.close()

if __name__ == "__main__":
    fix_duplicates()
