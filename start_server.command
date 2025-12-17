#!/bin/bash
cd "$(dirname "$0")"
export PATH=$PATH:/Users/sheikhhamza/Library/Python/3.9/bin


# Start Meilisearch
echo "Starting Meilisearch..."
if [ -f "./application/meilisearch" ]; then
    ./application/meilisearch --master-key masterKey > /dev/null 2>&1 &
    MEILI_PID=$!
    echo "Meilisearch started (PID: $MEILI_PID)"
else
    echo "Warning: Meilisearch binary not found in ./application. Search features may be limited."
fi

# Start Streamlit App (Non-blocking background process)
(
    echo "Starting AI Research Assistant (Streamlit)..."
    cd "BLD_AI_Deployment_Ready"

    # Check if pip is available
    if command -v pip3 &> /dev/null; then
        PIP_CMD="pip3"
    elif command -v pip &> /dev/null; then
        PIP_CMD="pip"
    fi

    if [ -n "$PIP_CMD" ]; then
         # Install requirements quietly
        echo "Checking/Installing Python requirements (this may take a while)..."
        $PIP_CMD install -r requirements.txt -q
        
        # Run Streamlit
        if [ $? -eq 0 ]; then
             echo "Python requirements installed. Starting Streamlit..."
             streamlit run app_sql.py --server.port 8501 --server.headless true > ../streamlit.log 2>&1 &
             STREAMLIT_PID=$!
             echo "AI Assistant started on http://localhost:8501 (PID: $STREAMLIT_PID)"
             
             # Wait for PID to be available to parent? No, subshell PID won't propagate easily to trap.
             # We will just rely on pkill cleanup or manual stop for now if script exits.
             # Or write PID to file.
             echo $STREAMLIT_PID > ../streamlit.pid
        else
             echo "Failed to install Python requirements."
        fi
    else
        echo "Error: pip not found. Cannot start AI Assistant."
    fi
) &

# We won't trap STREAMLIT_PID because it's in a subshell, but we can try to include it if we read from file.
# For robustness, we'll use pkill on exit for the user's convenience.

echo "Starting Digital BLD Server..."
echo "You can access the site at: http://127.0.0.1:8000"
echo "Press Ctrl+C to stop the server."

# Kill processes when this script exits
trap "kill $MEILI_PID 2>/dev/null; pkill -P $$; kill $(cat streamlit.pid 2>/dev/null) 2>/dev/null" EXIT

cd application
php artisan serve
