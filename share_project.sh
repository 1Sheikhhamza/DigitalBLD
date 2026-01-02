#!/bin/bash

# Live Share Helper Script
# This helps you share your local BLD project with specific instructions.

echo "=================================================="
echo "   DIGITAL BLD - LIVE SHARE HELPER"
echo "=================================================="
echo ""
echo "To share your project, you need TWO public links because your app runs on two ports:"
echo "1. Laravel Main App (Port 8000)"
echo "2. AI Service (Port 8501)"
echo ""

# Check for ngrok
if ! command -v ngrok &> /dev/null; then
    echo "⚠️  ngrok is not installed."
    echo "Please install it first: brew install ngrok/ngrok/ngrok"
    echo "Then sign up at https://dashboard.ngrok.com/signup"
    echo "And run the config command they provide (ngrok config add-authtoken ...)"
    exit 1
fi

echo "✅ ngrok found."
echo ""
echo "--- STEP 1: SHARE AI SERVICE (REQUIRED FIRST) ---"
echo "Run this command in a NEW terminal tab:"
echo "   ngrok http 8501"
echo ""
echo "Copy the https://....ngrok-free.app link it gives you."
echo "Paste it below:"
read -p "AI Public URL: " AI_URL

# Validate URL roughly
if [[ $AI_URL != http* ]]; then
    echo "❌ Invalid URL. It must start with http:// or https://"
    exit 1
fi

# Update .env
echo ""
echo "Updating .env file with new AI URL..."
# Cross-platform sed for macOS/Linux to replace the line starting with AI_SERVICE_URL
sed -i.bak "s|^AI_SERVICE_URL=.*|AI_SERVICE_URL=$AI_URL|" application/.env

echo "✅ .env updated."
echo "   (Backup created at application/.env.bak)"
echo ""

echo "--- STEP 2: SHARE MAIN APP ---"
echo "Run this command in a NEW terminal tab:"
echo "   ngrok http 8000"
echo ""
echo "👉 SHARE THE MAIN APP LINK (Port 8000) WITH YOUR FRIEND."
echo "The AI page will work automatically because we updated the config!"
echo ""
echo "⚠️  IMPORTANT: When you are done, revert the changes by running:"
echo "   sed -i.bak 's|^AI_SERVICE_URL=.*|AI_SERVICE_URL=http://127.0.0.1:8501|' application/.env"
echo "=================================================="
