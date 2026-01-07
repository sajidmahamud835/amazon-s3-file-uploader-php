# CloudFlow - Modern S3 File Uploader (PHP)

[![Deploy to Render](https://render.com/images/deploy-to-render-button.svg)](https://render.com/deploy?repo=https://github.com/sajidmahamud835/amazon-s3-file-uploader-php)

A secure, modern, and beautifully designed file uploader for Amazon S3, built with PHP 8.2 and the AWS SDK.

## Features

*   **Premium Glassmorphism UI:** Stunning visual design with animations and responsiveness.
*   **Drag & Drop:** Intuitive file selection with drag and drop support.
*   **Instant Previews:** Client-side image previews before uploading.
*   **Demo Mode:** No AWS credentials? Users can enter their own for testing.
*   **Secure:** CSRF protection, MIME validation, env-based configuration.
*   **Containerized:** Docker & Render support for easy deployment.

## Demo Mode

If no AWS credentials are configured in the environment, the app will display a form where users can enter their own credentials for testing. **Credentials are stored in session only** and are never persisted or logged.

## Quick Start

### Deploy to Render (One-Click)

Click the button above to deploy instantly to Render. Leave environment variables empty for demo mode.

### Local (Docker)

```bash
git clone https://github.com/sajidmahamud835/amazon-s3-file-uploader-php.git
cd amazon-s3-file-uploader-php
cp .env.example .env  # Edit with your AWS credentials, or leave empty for demo mode
docker-compose up -d --build
```
Open `http://localhost:8080`.

### Local (PHP)

```bash
composer install
cd public
php -S localhost:8000
```
Open `http://localhost:8000`.

## Directory Structure

*   `src/`: Core PHP logic (Config, S3Uploader).
*   `public/`: Entry point and assets.
*   `templates/`: HTML views.
*   `Dockerfile`, `docker-compose.yml`, `render.yaml`: DevOps configs.
