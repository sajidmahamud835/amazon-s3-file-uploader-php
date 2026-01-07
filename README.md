# CloudFlow - Modern S3 File Uploader (PHP)

A secure, modern, and beautifully designed file uploader for Amazon S3, built with PHP 8.2 and the AWS SDK.

## Features

*   **Premium Glassmorphism UI:** Stunning visual design with animations and responsiveness.
*   **Drag & Drop:** Intuitive file selection with drag and drop support.
*   **Instant Previews:** Client-side image previews before uploading.
*   **Secure:**
    *   CSRF Protection.
    *   Strict MIME type and file size validation.
    *   Environment-based configuration.
*   **Containerized:** Docker support for easy deployment.
*   **Clean Architecture:** Refactored into a structured MVC-like pattern.

## Prerequisites

*   **Docker** (Recommended)
*   *OR* **PHP 8.2+** and **Composer**

## Quick Start (Docker)

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/sajidmahamud835/amazon-s3-file-uploader-php.git
    cd amazon-s3-file-uploader-php
    ```

2.  **Configure Environment:**
    ```bash
    cp .env.example .env
    # Edit .env with your AWS Credentials
    ```

3.  **Run with Docker Compose:**
    ```bash
    docker-compose up -d --build
    ```

4.  **Open Browser:**
    Navigate to `http://localhost:8080`.

## Manual Setup (Without Docker)

1.  **Install Dependencies:**
    ```bash
    composer install
    ```

2.  **Run Built-in Server:**
    ```bash
    cd public
    php -S localhost:8000
    ```
    *Note: The document root is now the `public/` directory.*

3.  **Open Browser:**
    Navigate to `http://localhost:8000`.

## Directory Structure

*   `src/`: Core PHP logic (Config, S3Uploader).
*   `public/`: Public entry entry point and assets.
*   `templates/`: HTML views.
*   `Dockerfile` & `docker-compose.yml`: DevOps configuration.
