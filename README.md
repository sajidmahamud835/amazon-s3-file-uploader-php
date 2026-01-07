# Amazon S3 File Uploader (PHP)

This utility allows you to securely upload files to Amazon S3 using PHP 8.2+ and the AWS SDK for PHP v3.

## Prerequisites

*   **PHP 8.2** or higher
*   **Composer** (for dependency management)
*   An **AWS Account** with an S3 Bucket and IAM User credentials.

## Setup

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/sajidmahamud835/amazon-s3-file-uploader-php.git
    cd amazon-s3-file-uploader-php
    ```

2.  **Install Dependencies:**
    ```bash
    composer install
    ```

3.  **Configure Environment:**
    *   Copy the example environment file:
        ```bash
        cp .env.example .env
        ```
    *   Edit `.env` and add your AWS credentials:
        ```ini
        AWS_BUCKET=your_bucket_name
        AWS_REGION=us-east-1
        AWS_ACCESS_KEY_ID=your_access_key
        AWS_SECRET_ACCESS_KEY=your_secret_key
        ```
    *   **Security Note:** Never commit your `.env` file to version control. It is already ignored in `.gitignore`.

## Running the Application

You can run the built-in PHP server for testing:

```bash
php -S localhost:8000
```

Open your browser and navigate to `http://localhost:8000/upload.php`.

## Security Features

*   **MIME Type Validation:** Verifies the actual file type (not just extension).
*   **File Size Limit:** Enforces a 5MB maximum upload size.
*   **Filename Sanitization:** Generates a unique filename (`uniqid`) to prevent overwrites and directory traversal attacks.
*   **Secure Credentials:** Uses environment variables; no hardcoded keys.
*   **Error Handling:** Catches AWS exceptions and hides stack traces from the user.

## Changelog

### 2026-01-08
*   **Restoration:** Restored `main` branch from default.
*   **Security:** Added robust CSRF protection using `hash_equals` and session tokens.
*   **Optimization:** Improved MIME type validation performance using `isset()` instead of `in_array()`.
*   **UX:** Added inline client-side validation for file size and type.
*   **UX:** Added instant image preview before upload.
*   **Cleanup:** Removed blocking `alert()` calls in favor of non-intrusive UI feedback.
