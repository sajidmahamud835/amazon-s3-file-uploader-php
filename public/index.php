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
    // Silently continue - demo mode will handle missing config
}

// Enable demo mode if session credentials exist
if (Config::isDemoMode()) {
    Config::enableDemoMode();
}

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$messageType = '';

// Handle credential submission (demo mode)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'set_credentials') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = "Error: Invalid CSRF token.";
        $messageType = 'error';
    } else {
        Config::setSessionCredentials([
            'bucket' => $_POST['bucket'] ?? '',
            'region' => $_POST['region'] ?? 'us-east-1',
            'access_key' => $_POST['access_key'] ?? '',
            'secret_key' => $_POST['secret_key'] ?? '',
        ]);
        // Redirect to avoid form resubmission
        header('Location: /');
        exit;
    }
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
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

            $url = $uploader->upload($_FILES['file']);
            
            $message = "Success! File uploaded to S3.";
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

// Determine which view to show
$showConfigForm = !Config::hasAwsCredentials();

$data = [
    'message' => $message,
    'messageType' => $messageType,
    'csrf_token' => $_SESSION['csrf_token'],
    'isDemoMode' => Config::isDemoMode()
];

extract($data);

ob_start();
if ($showConfigForm) {
    require __DIR__ . '/../templates/config_form.php';
} else {
    require __DIR__ . '/../templates/upload_form.php';
}
$content = ob_get_clean();

require __DIR__ . '/../templates/layout.php';
