<div class="upload-screen">
    <header class="uploader-header">
        <div class="logo-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 16L12 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 11L12 8 15 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 17H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1>Demo Mode</h1>
        <p>Enter your AWS credentials to test the uploader.</p>
    </header>

    <div class="notification warning slide-in">
        ⚠️ <strong>Security Notice:</strong> Credentials are stored in your session only and are not logged or persisted.
    </div>

    <form action="" method="post" class="config-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <input type="hidden" name="action" value="set_credentials">

        <div class="form-group">
            <label for="bucket">S3 Bucket Name</label>
            <input type="text" name="bucket" id="bucket" required placeholder="my-bucket-name">
        </div>

        <div class="form-group">
            <label for="region">AWS Region</label>
            <input type="text" name="region" id="region" value="us-east-1" required placeholder="us-east-1">
        </div>

        <div class="form-group">
            <label for="access_key">Access Key ID</label>
            <input type="text" name="access_key" id="access_key" required placeholder="AKIAIOSFODNN7EXAMPLE">
        </div>

        <div class="form-group">
            <label for="secret_key">Secret Access Key</label>
            <input type="password" name="secret_key" id="secret_key" required placeholder="wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY">
        </div>

        <button type="submit" class="upload-btn">
            <span>Connect & Continue</span>
        </button>
    </form>

    <p class="demo-note">
        Don't have AWS credentials? <a href="https://docs.aws.amazon.com/IAM/latest/UserGuide/id_credentials_access-keys.html" target="_blank">Learn how to create them</a>.
    </p>
</div>
