# DigitalBLD - Technical Documentation

## Project Overview
**DigitalBLD** is a legal tech platform that provides access to legal resources, judgments, and an AI-powered legal assistant. The application is built as a hybrid monolith using **Laravel (PHP)** for the core application and **Streamlit (Python)** for the AI microservice, running within a **Dockerized** environment.

---

## 🏗 Architecture & Stack

### Core Application (Backend & Frontend)
- **Framework**: Laravel 11.x
- **Language**: PHP 8.2
- **Frontend**: Blade Templates + Vanilla JS / Vue.js (via Vite)
- **Database**: mysql:8.0
- **Search Engine**: Meilisearch v1.3
- **Web Server**: Nginx (Alpine)

### AI Research Assistant (Microservice)
- **Framework**: Streamlit
- **Language**: Python 3.9+
- **AI Models**: Google Gemini (via `google-generativeai`), ChromaDB (Vector Store)
- **Communication**: Shares the MySQL database with the main Laravel app.

---

## 📦 Dependencies

### Backend (Composer - `application/composer.json`)
Key dependencies managed by Composer:
- **`laravel/framework`**: ^11.9
- **`laravel/passport`**: ^12.0 (API Authentication)
- **`laravel/reverb`**: ^1.0 (Real-time broadcasting)
- **`laravel/scout`**: ^10.22 (Search abstraction)
- **`meilisearch/meilisearch-php`**: ^1.16 (Search client)
- **`spatie/laravel-permission`**: ^6.16 (RBAC)
- **`google/cloud-vision`**: ^2.0 (OCR/Image analysis)
- **`barryvdh/laravel-dompdf`**: ^3.1 (PDF Generation)
- **`smalot/pdfparser`**: ^2.12 (PDF Parsing)

### Frontend (NPM - `application/package.json`)
Key dependencies managed by NPM/Vite:
- **`vite`**: ^5.0 (Build tool)
- **`laravel-vite-plugin`**: ^1.0
- **`axios`**: ^1.7.4
- **`laravel-echo`** / **`pusher-js`**: Real-time events.

### AI Service (Python - `BLD_AI_Deployment_Ready/requirements.txt`)
- **`streamlit`**: Web interface for AI tools.
- **`google-generativeai`**: Interface for Gemini API.
- **`chromadb`**: Vector database for RAG (Retrieval-Augmented Generation).
- **`mysql-connector-python`**: Database connectivity.

---

## ⚙️ Environment Details

### Docker Services (`docker-compose.yml`)
The project runs 5 linked containers:
1.  **`app`** (PHP-FPM 8.2): Core application logic.
2.  **`web`** (Nginx): Web server and reverse proxy.
3.  **`db`** (MySQL 8.0): Primary database.
    - **Port**: 3306 (Internal), 3306 (External)
4.  **`meilisearch`** (Meilisearch v1.3): Fast text search engine.
    - **Port**: 7700
5.  **`streamlit`** (Python): AI Assistant interface.
    - **Port**: 8501

### Critical Environment Variables (`.env`)
Required setup in `application/.env`:
```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=bldlegalized_bld
DB_USERNAME=root
DB_PASSWORD=<secure-password>

# Search
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://meilisearch:7700
MEILISEARCH_KEY=masterKey

# AI Keys (Application & Python)
GOOGLE_API_KEY=<your-gemini-key>
```

---

## 🚀 Deployment & Build Process

The project uses a **multi-stage Docker build** to optimize images.

1.  **Frontend Build**: Node.js stage compiles assets (`npm run build`).
2.  **Backend Build**: Composer installs production dependencies (`--no-dev`).
3.  **Final Image**: PHP-FPM image containing code + built assets.

**Deployment Script**: `./deploy.sh`
- Automates git pull, container rebuilds, migrations, and caching.

**Entrypoint**: `application/production_entrypoint.sh`
- Automatically handles `config:cache`, `route:cache`, and `view:cache` on container startup.

---

## 📂 Directory Structure
- **`/application`**: Main Laravel Codebase.
- **`/BLD_AI_Deployment_Ready`**: Python/Streamlit AI Code.
- **`/docker`**: Nginx and other config files.
- **`/docker-compose.yml`**: Orchestration config.
