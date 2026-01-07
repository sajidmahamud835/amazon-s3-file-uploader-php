<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';

use App\Config;
use App\S3Uploader;
use Aws\Exception\AwsException;

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

// Handle "Skip for Later" action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'skip_config') {
    if (isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['skip_config'] = true;
        header('Location: /');
        exit;
    }
}

// Handle credential submission with connection test
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'set_credentials') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = "Error: Invalid CSRF token.";
        $messageType = 'error';
    } else {
        $credentials = [
            'bucket' => $_POST['bucket'] ?? '',
            'region' => $_POST['region'] ?? 'us-east-1',
            'access_key' => $_POST['access_key'] ?? '',
            'secret_key' => $_POST['secret_key'] ?? '',
        ];
        
        // Test connection before saving
        try {
            $testUploader = new S3Uploader(
                $credentials['bucket'],
                $credentials['region'],
                $credentials['access_key'],
                $credentials['secret_key']
            );
            $testUploader->testConnection();
            
            // Connection successful - save credentials
            Config::setSessionCredentials($credentials);
            header('Location: /');
            exit;
            
        } catch (AwsException $e) {
            $awsError = $e->getAwsErrorMessage() ?: $e->getMessage();
            $message = "AWS Error: " . htmlspecialchars($awsError);
            $messageType = 'error';
        } catch (\Exception $e) {
            $message = "Connection failed: " . htmlspecialchars($e->getMessage());
            $messageType = 'error';
        }
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

        } catch (AwsException $e) {
            $awsError = $e->getAwsErrorMessage() ?: $e->getMessage();
            $message = "AWS Error: " . htmlspecialchars($awsError);
            $messageType = 'error';
        } catch (\RuntimeException $e) {
            $message = htmlspecialchars($e->getMessage());
            $messageType = 'error';
        } catch (\Exception $e) {
            $message = "Upload failed. Please check your credentials and try again.";
            $messageType = 'error';
        }
    }
}

// Determine which view to show
$skipConfig = isset($_SESSION['skip_config']) && $_SESSION['skip_config'] === true;
$showConfigForm = !Config::hasAwsCredentials() && !$skipConfig;

$data = [
    'message' => $message,
    'messageType' => $messageType,
    'csrf_token' => $_SESSION['csrf_token'],
    'isDemoMode' => Config::isDemoMode(),
    'isSkipMode' => $skipConfig
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
