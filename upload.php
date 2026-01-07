<?php

session_start();

require 'vendor/autoload.php';



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

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
<<<<<<< HEAD
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

            // Optimization: Use isset() for O(1) lookup instead of in_array(array_keys()) which is O(n) + allocation
            if (!isset($allowedMimeTypes[$mimeType])) {
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
        /* Palette Enhancements */
        #file-feedback { margin-bottom: 1rem; }
        .feedback-error { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; font-weight: bold; }
        .preview-container { margin-top: 0.5rem; }
        .preview-image { max-width: 150px; max-height: 150px; border-radius: 4px; border: 1px solid #dee2e6; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <h1>Upload File to S3</h1>

    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, 'Success') !== false ? 'success' : 'error'; ?>" role="alert">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Client-side error container for better accessibility -->
    <div id="client-message" class="message error" style="display: none;" role="alert" aria-live="assertive"></div>

    <form action="" method="post" enctype="multipart/form-data" id="uploadForm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <label for="file">Select file to upload (Max 5MB):</label>
        <input type="file" name="file" id="file" required accept=".jpg,.jpeg,.png,.gif,.pdf,.txt" aria-describedby="file-help">
        <small id="file-help" style="display: block; margin-bottom: 0.5rem; color: #666;">Allowed: JPG, PNG, GIF, PDF, TXT</small>

        <!-- Palette: Immediate feedback container -->
        <div id="file-feedback" aria-live="polite"></div>

        <button type="submit" id="submitBtn">
            <span class="spinner" id="spinner"></span>
            <span id="btnText">Upload</span>
        </button>
    </form>

    <script>
        const fileInput = document.getElementById('file');
        const feedback = document.getElementById('file-feedback');
        const submitBtn = document.getElementById('submitBtn');

        fileInput.addEventListener('change', function() {
            feedback.innerHTML = ''; // Clear previous
            submitBtn.disabled = false;

            if (this.files && this.files[0]) {
                const file = this.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB

                // Validation
                if (file.size > maxSize) {
                    feedback.innerHTML = '<div class="feedback-error">⚠️ File is too large (Max 5MB).</div>';
                    submitBtn.disabled = true;
                    this.setAttribute('aria-invalid', 'true');
                    return;
                }
                this.setAttribute('aria-invalid', 'false');

                // Preview (if image)
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'preview-image';
                        img.alt = 'Preview: ' + file.name;
                        const container = document.createElement('div');
                        container.className = 'preview-container';
                        container.appendChild(img);
                        feedback.appendChild(container);
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Non-image feedback
                    const info = document.createElement('div');
                    info.className = 'preview-container';
                    info.style.color = '#0056b3';
                    info.textContent = '📄 Ready to upload: ' + file.name;
                    feedback.appendChild(info);
                }
            }
        });

        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            const spinner = document.getElementById('spinner');
            const btnText = document.getElementById('btnText');

            // Re-check size just in case (though button should be disabled)
            if (fileInput.files.length > 0) {
                if (fileInput.files[0].size > 5 * 1024 * 1024) {
                    e.preventDefault();
                    return;
                }
            }

            // Show loading state
            submitBtn.disabled = true;
            spinner.style.display = 'inline-block';
            btnText.textContent = 'Uploading...';
        });
    </script>
</body>
</html>
