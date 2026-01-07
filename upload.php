<?php

session_start();

require 'vendor/autoload.php';

// Security Headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');



// Generate CSRF Token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configuration
$bucket = $_ENV['AWS_BUCKET'] ?? null;
$region = $_ENV['AWS_REGION'] ?? 'us-east-1';
$accessKeyId = $_ENV['AWS_ACCESS_KEY_ID'] ?? null;
$secretAccessKey = $_ENV['AWS_SECRET_ACCESS_KEY'] ?? null;

if (!$bucket || !$accessKeyId || !$secretAccessKey) {
    die("Error: AWS credentials or bucket name missing from .env configuration.");
}

$message = '';



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // Validate CSRF Token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Error: Invalid CSRF token.");
    }

    $file = $_FILES['file'];

    // 1. Validation: Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = "Upload failed with error code: " . $file['error'];
    } else {
        // 2. Validation: File Size (Max 5MB)
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxFileSize) {
            $message = "Error: File size exceeds the 5MB limit.";
        } else {
            // 3. Validation: MIME Type
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);

            // Allow only specific types (adjust as needed)
            $allowedMimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'application/pdf' => 'pdf',
                'text/plain' => 'txt'
            ];

            if (!in_array($mimeType, array_keys($allowedMimeTypes))) {
                $message = "Error: Invalid file type ($mimeType). Allowed: JPG, PNG, GIF, PDF, TXT.";
            } else {
                // 4. Sanitization: Generate unique filename
                // Use the safe extension derived from the MIME type map
                $extension = $allowedMimeTypes[$mimeType];
                $uniqueName = uniqid('upload_', true) . '.' . $extension;

                // 5. Upload to S3
                try {
                    $s3 = new S3Client([
                        // Optimization: Use specific version instead of 'latest' to avoid lookup overhead
                        'version' => '2006-03-01',
                        'region'  => $region,
                        'credentials' => [
                            'key'    => $accessKeyId,
                            'secret' => $secretAccessKey,
                        ],
                    ]);

                    $result = $s3->putObject([
                        'Bucket' => $bucket,
                        'Key'    => $uniqueName,
                        'SourceFile' => $file['tmp_name'],
                        'ACL'    => 'private', // Secure default
                        // 'ContentType' => $mimeType // Optional: verify if needed
                    ]);

                    $message = "Success! File uploaded to S3. Object URL: " . $result['ObjectURL'];

                } catch (AwsException $e) {
                    // Log the error securely (not exposing to user in prod)
                    error_log($e->getMessage());
                    $message = "Error uploading to S3: " . $e->getAwsErrorMessage();
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    $message = "General Error: An unexpected error occurred.";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure S3 File Uploader</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 2rem auto; padding: 1rem; }
        .message { padding: 1rem; margin-bottom: 1rem; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        form { border: 1px solid #ddd; padding: 2rem; border-radius: 8px; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input[type="file"] { margin-bottom: 1rem; display: block; }
        button { background-color: #007bff; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; transition: background-color 0.2s; }
        button:hover:not(:disabled) { background-color: #0056b3; }
        button:disabled { background-color: #93c5fd; cursor: not-allowed; }
        .spinner {
            width: 1em; height: 1em; border: 2px solid #fff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: none;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <h1>Upload File to S3</h1>

    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, 'Success') !== false ? 'success' : 'error'; ?>" role="alert">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data" id="uploadForm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <label for="file">Select file to upload (Max 5MB):</label>
        <input type="file" name="file" id="file" required accept=".jpg,.jpeg,.png,.gif,.pdf,.txt" aria-describedby="file-help">
        <small id="file-help" style="display: block; margin-bottom: 1rem; color: #666;">Allowed: JPG, PNG, GIF, PDF, TXT</small>

        <button type="submit" id="submitBtn">
            <span class="spinner" id="spinner"></span>
            <span id="btnText">Upload</span>
        </button>
    </form>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const btnText = document.getElementById('btnText');
            const fileInput = document.getElementById('file');

            // Client-side size check
            if (fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size;
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (fileSize > maxSize) {
                    e.preventDefault();
                    alert('File is too large. Max 5MB.');
                    return;
                }
            }

            // Show loading state
            btn.disabled = true;
            spinner.style.display = 'inline-block';
            btnText.textContent = 'Uploading...';
        });
    </script>
</body>
</html>
