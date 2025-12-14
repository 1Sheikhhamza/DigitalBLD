#!/bin/bash
cd "$(dirname "$0")/application"
echo "Starting Meilisearch..."
# Check if meilisearch exists and start it
if [ -f "./meilisearch" ]; then
    ./meilisearch --master-key masterKey > /dev/null 2>&1 &
    MEILI_PID=$!
    echo "Meilisearch started (PID: $MEILI_PID)"
else
    echo "Warning: Meilisearch binary not found. Search features may be limited."
fi

echo "Starting Digital BLD Server..."
echo "You can access the site at: http://127.0.0.1:8000"
echo "Press Ctrl+C to stop the server."

# Kill Meilisearch when this script exits
trap "kill $MEILI_PID 2>/dev/null" EXIT

php artisan serve
