# BLD.AI - Deployment Package

This folder contains everything needed to run the BLD.AI Legal Assistant.

## Contents
- **app_sql.py**: The main application code (Single-file, embedded view).
- **logo.png**: The branding logo.
- **requirements.txt**: Python dependencies.
- **.streamlit/**: Configuration for Theme (Light Mode).

## How to Run

1.  **Install Requirements**:
    ```bash
    pip install -r requirements.txt
    ```

2.  **Run the App**:
    ```bash
    streamlit run app_sql.py
    ```

## Cloud Deployment (Streamlit Cloud)

1.  **Deploy**: Connect your GitHub repo to Streamlit Cloud.
2.  **Database Config**:
    *   Go to App Settings -> **Secrets**.
    *   Copy content from `secrets.toml.example`.
    *   Fill in your REAL database details (Host, User, Password).
    *   *The app will automatically switch to using these secrets when detected.*

## Functionality
- **Chat Interface**: Ask legal questions.
- **Source Panel**: Right-side panel shows found cases.
- **Embedded View**: Clicking "View Full Case" opens the judgment in full-screen reader mode.
