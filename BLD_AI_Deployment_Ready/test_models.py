import requests
import os

# New Key provided by user
api_key = "AIzaSyDQ9Gqp49dnUPXwVGDyZQh4SeS9Tiz4IXE"

url = f"https://generativelanguage.googleapis.com/v1beta/models?key={api_key}"

print(f"Testing Key: {api_key[:10]}...")

try:
    response = requests.get(url)
    if response.status_code == 200:
        print("✅ SUCCESS! Key is valid.")
        models = response.json().get('models', [])
        print("AVAILABLE MODELS:")
        for m in models:
            if 'generateContent' in m['supportedGenerationMethods']:
                print(f"- {m['name']}")
    else:
        print(f"❌ FAILED. Status: {response.status_code}")
        print(f"Response: {response.text}")
except Exception as e:
    print(f"Failed: {e}")
