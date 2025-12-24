# Deployment Guide for DigitalBLD

This guide outlines the steps to deploy the **DigitalBLD** application to a production server using Docker.

## Prerequisites
- A Linux server (Ubuntu 20.04/22.04 recommended).
- [Docker](https://docs.docker.com/engine/install/) and [Docker Compose](https://docs.docker.com/compose/install/) installed.
- Git installed.

## 1. Setup

1.  **Clone the Repository**:
    ```bash
    git clone <your-repo-url>
    cd DigitalBLD_Launch_Ready
    ```

2.  **Environment Configuration**:
    Copy the example environment file and configure it for production.
    ```bash
    cp application/.env.example application/.env
    nano application/.env
    ```
    **Critical Settings**:
    - `APP_ENV=production`
    - `APP_DEBUG=false`
    - `APP_URL=https://your-domain.com`
    - `DB_HOST=db` (matches docker-compose service name)
    - `DB_PASSWORD=<secue-password>`
    - `MEILISEARCH_HOST=http://meilisearch:7700`

3.  **Permissions**:
    Ensure the `deploy.sh` script is executable.
    ```bash
    chmod +x deploy.sh
    ```

## 2. Deployment

To deploy the application (build containers, run migrations, cache config), simply run:

```bash
./deploy.sh
```

This script will:
- Pull the latest code (if using git).
- Build the Docker images (including compiling frontend assets via Node.js).
- Start the services.
- Run database migrations.
- Optimize and cache Laravel configurations.

## 3. Post-Deployment Checks

- **SSL/HTTPS**: Ensure you have set up SSL. You may need to configure Certbot/Let's Encrypt on the Nginx container or use a reverse proxy (like Nginx Proxy Manager or Cloudflare) in front of the application.
- **Queues**: If your application uses queues, ensure the queue worker is running. You may need to add a `queue` service to `docker-compose.yml` or run a supervisor process inside the `app` container.

## 4. Maintenance

- **View Logs**:
    ```bash
    docker-compose logs -f app
    docker-compose logs -f web
    ```
- **Run Artisan Commands**:
    ```bash
    docker-compose exec app php artisan <command>
    ```
- **Update Application**:
    Run `./deploy.sh` again to pull changes and redeploy.
