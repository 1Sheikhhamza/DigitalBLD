#!/bin/bash
set -e

# Deployment Script for DigitalBLD

echo "Starting deployment..."

# 1. Pull latest changes (if git is used)
if [ -d ".git" ]; then
    echo "Pulling latest changes from git..."
    git pull origin main # Adjust branch if needed
else
    echo "No git repository found, skipping git pull."
fi

# 2. Build and restart containers
echo "Rebuilding and restarting containers..."
# Use --no-cache to force rebuild of updated code/assets
docker-compose -f docker-compose.yml up -d --build --force-recreate

# 3. Wait for database to be ready (simple sleep or check)
echo "Waiting for services to stabilize..."
sleep 10

# 4. Run Migrations & Optimizations inside the container
echo "Running database migrations..."
docker-compose exec -T app php artisan migrate --force

echo "Clearing and rebuilding caches..."
# These are redundant if run in entrypoint, but good for hot-reloads without container restart
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache
docker-compose exec -T app php artisan storage:link

echo "Deployment complete!"
