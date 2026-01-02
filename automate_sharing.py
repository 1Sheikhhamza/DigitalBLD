import subprocess
import time
import requests
import re
import os

def start_cloudflared_tunnel(port):
    print(f"Starting Cloudflare tunnel for port {port}...")
    log_file = open(f"cf_{port}.log", "w")
    # cloudflared tunnel --url http://localhost:8501
    process = subprocess.Popen(["cloudflared", "tunnel", "--url", f"http://localhost:{port}"], stdout=log_file, stderr=log_file)
    return process, log_file

def get_cloudflared_url(log_filename, retries=20):
    print("Waiting for Cloudflare tunnel URL...")
    for i in range(retries):
        time.sleep(1)
        if os.path.exists(log_filename):
            with open(log_filename, "r") as f:
                content = f.read()
                # Match "https://<random>.trycloudflare.com"
                match = re.search(r"(https://[a-zA-Z0-9-]+\.trycloudflare\.com)", content)
                if match:
                    return match.group(1)
    return None

def update_env_file(ai_url, app_url=None):
    env_path = "application/.env"
    print(f"Updating {env_path}...")
    
    with open(env_path, "r") as f:
        content = f.read()
    
    # Update AI URL
    if "AI_SERVICE_URL=" in content:
        content = re.sub(r"AI_SERVICE_URL=.*", f"AI_SERVICE_URL={ai_url}", content)
    else:
        content += f"\nAI_SERVICE_URL={ai_url}"

    # Update APP URL (if provided)
    if app_url:
        if "APP_URL=" in content:
             content = re.sub(r"APP_URL=.*", f"APP_URL={app_url}", content)
        else:
             content += f"\nAPP_URL={app_url}"
        
    with open(env_path, "w") as f:
        f.write(content)

def main():
    print("--- AUTOMATED LIVE SHARE SETUP (Hybrid: ngrok + cloudflared) ---")
    
    # 1. Kill existing processes
    subprocess.run(["pkill", "ngrok"], stderr=subprocess.DEVNULL)
    subprocess.run(["pkill", "cloudflared"], stderr=subprocess.DEVNULL)
    time.sleep(1)

    # 2. Start AI Tunnel (cloudflared -> Port 8501)
    # Cloudflare Tunnels (trycloudflare.com) have NO warning pages
    cf_process, cf_log = start_cloudflared_tunnel(8501)
    ai_url = get_cloudflared_url("cf_8501.log")
    
    if not ai_url:
        print("❌ Failed to get AI URL (cloudflared).")
    else:
        print(f"✅ AI Service available at: {ai_url}")

    # 3. Update Env
    # Now passing both URLs
    # Note: We haven't fetched App URL yet in the original flow, but for now just update AI.
    # Actually, we need to correct the flow to update APP_URL after fetching it.
    if ai_url:
        update_env_file(ai_url)

    # 4. Start App Tunnel (ngrok -> Port 8000)
    # using ngrok for main app as it's more stable/faster
    print("Starting ngrok for Main App...")
    ngrok_log = open("ngrok_app.log", "w")
    default_config = os.path.expanduser("~/Library/Application Support/ngrok/ngrok.yml")
    ngrok_process = subprocess.Popen(["ngrok", "http", "8000", "--config", default_config], stdout=ngrok_log, stderr=ngrok_log)
    
    app_url = None
    for i in range(15):
        time.sleep(2)
        try:
            response = requests.get("http://127.0.0.1:4040/api/tunnels")
            if response.status_code == 200:
                data = response.json()
                tunnels = data.get('tunnels', [])
                if len(tunnels) >= 2:
                    for t in tunnels:
                        if t['config']['addr'].endswith("8501") or "8501" in t['config']['addr']:
                            ai_url = t['public_url']
                        elif t['config']['addr'].endswith("8000") or "8000" in t['config']['addr']:
                            app_url = t['public_url']
                    
                    if ai_url and app_url:
                        break
        except Exception:
            pass
        print(f" Attempt {i+1}...")

    if not ai_url: # Check if AI URL was lost or not found via ngrok
        print("❌ Failed to get AI Service URL from ngrok.")
        return # Exit if AI URL is critical and not found

    if not app_url:
        print("❌ Failed to get Main App URL from ngrok.")
        return # Exit if App URL is critical and not found
    else:
        print(f"✅ Main App available at: {app_url}")
        # Update .env with both URLs now
        update_env_file(ai_url, app_url)

    print("\n" + "="*50)
    print("🚀 LIVE SHARE READY!")
    print("="*50)
    print(f"📂 Main Website Link: {app_url}  <-- SHARE THIS")
    print(f"🤖 AI Service Link:   {ai_url}")
    print("="*50)
    print("Note: Keep this script running. Press Ctrl+C to stop.")

if __name__ == "__main__":
    main()
