<?php

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
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
                        'version' => 'latest',
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

                    $message = "Success! File uploaded to S3. Object URL: " . htmlspecialchars($result['ObjectURL']);

                } catch (AwsException $e) {
                    // Log the error securely (not exposing to user in prod)
                    error_log($e->getMessage());
                    $message = "Error uploading to S3: " . $e->getAwsErrorMessage();
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    $message = "General Error: " . $e->getMessage();
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
        button { background-color: #007bff; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h1>Upload File to S3</h1>

    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, 'Success') !== false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <label for="file">Select file to upload (Max 5MB):</label>
        <input type="file" name="file" id="file" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
