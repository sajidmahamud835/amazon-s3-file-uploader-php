<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';

use App\Config;
use App\S3Uploader;

// Security Headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');

try {
    Config::load(__DIR__ . '/../');
} catch (\Exception $e) {
    die("Configuration Error: " . $e->getMessage());
}

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // CSRF Check
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = "Error: Invalid CSRF token.";
        $messageType = 'error';
    } else {
        try {
            $uploader = new S3Uploader(
                Config::require('AWS_BUCKET'),
                Config::require('AWS_REGION'),
                Config::require('AWS_ACCESS_KEY_ID'),
                Config::require('AWS_SECRET_ACCESS_KEY')
            );

            // Optional: You can customize allowed types here if needed, or rely on defaults
            $url = $uploader->upload($_FILES['file']);
            
            $message = "Success! File uploaded to: " . $url;
            $messageType = 'success';

        } catch (\RuntimeException $e) {
            $message = $e->getMessage();
            $messageType = 'error';
        } catch (\Exception $e) {
            $message = "An unexpected error occurred.";
            $messageType = 'error';
        }
    }
}

// Render View
// Simple template engine
$data = [
    'message' => $message,
    'messageType' => $messageType,
    'csrf_token' => $_SESSION['csrf_token']
];

// Extract data to variables for the view
extract($data);

// Buffer output to include layout
ob_start();
require __DIR__ . '/../templates/upload_form.php';
$content = ob_get_clean();

require __DIR__ . '/../templates/layout.php';
