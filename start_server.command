#!/bin/bash
cd "$(dirname "$0")/application"
echo "Starting Digital BLD Server..."
echo "You can access the site at: http://127.0.0.1:8000"
echo "Press Ctrl+C to stop the server."
php artisan serve
