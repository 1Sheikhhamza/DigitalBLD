import mysql.connector
import os
from dotenv import load_dotenv

load_dotenv()

# Use the same config logic as app_sql.py
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

def debug():
    conn = get_db_connection()
    if not conn:
        return

    cursor = conn.cursor(dictionary=True)

    print("--- 1. Checking Modules ---")
    cursor.execute("SELECT * FROM package_feature_modules WHERE slug LIKE '%ai%'")
    modules = cursor.fetchall()
    for m in modules:
        print(m)
    if not modules:
        print("NO 'ai-research' module found!")

    print("\n--- 2. Checking Features ---")
    cursor.execute("SELECT * FROM package_features")
    features = cursor.fetchall()
    for f in features:
        print(f)

    print("\n--- 3. Checking Subscriptions (Active) ---")
    cursor.execute("SELECT * FROM subscriptions WHERE status=1 LIMIT 5")
    subs = cursor.fetchall()
    for s in subs:
        print(f"Subscriber {s['subscriber_id']} -> Package {s['package_id']}")

    print("\n--- 4. Checking Package-Feature-Module Links ---")
    query = """
    SELECT 
        p.name as package,
        pf.name as feature,
        pfm.name as module,
        pfm.slug
    FROM packages p
    LEFT JOIN package_feature_relations pfr ON p.id = pfr.package_id
    LEFT JOIN package_features pf ON pfr.feature_id = pf.id
    LEFT JOIN package_feature_modules pfm ON pf.id = pfm.feature_id
    WHERE pfm.slug = 'ai-research' OR pfm.slug IS NULL
    """
    cursor.execute(query)
    links = cursor.fetchall()
    for l in links:
        print(l)

    conn.close()

if __name__ == "__main__":
    debug()
