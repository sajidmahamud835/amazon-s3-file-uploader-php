<div class="upload-screen">
    <header class="uploader-header">
        <div class="logo-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 16L12 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 11L12 8 15 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 17H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M19 19H19.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M4.5 19H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1>Upload Files</h1>
        <p>Securely push your data to the cloud.</p>
    </header>

    <?php if (!empty($message)): ?>
        <div class="notification <?php echo $messageType; ?> slide-in">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($isSkipMode) && $isSkipMode): ?>
        <div class="notification warning slide-in">
            ⚠️ <strong>Preview Mode:</strong> No credentials configured. Upload will fail. <a href="?reset=1">Configure credentials</a>
        </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data" id="uploadForm" class="upload-zone">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <input type="file" name="file" id="fileInput" class="hidden-input" accept=".jpg,.jpeg,.png,.gif,.pdf,.txt" required>
        
        <div class="drop-area" id="dropArea">
            <div class="icon-container">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
            </div>
            <h3>Drag & Drop your file here</h3>
            <p>or <span class="browse-link">browse files</span></p>
            <p class="limit-text">Max size: 5MB • JPG, PNG, PDF</p>
        </div>

        <div id="filePreview" class="file-preview hidden">
            <div class="preview-card">
                <div class="preview-icon"></div>
                <div class="file-info">
                    <span class="file-name">example.jpg</span>
                    <span class="file-size">2.4 MB</span>
                </div>
                <button type="button" class="remove-btn" id="removeFile">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="progress-container hidden">
                <div class="progress-bar" id="progressBar"></div>
            </div>
        </div>

        <button type="submit" class="upload-btn" id="submitBtn" disabled>
            <span>Upload File</span>
            <div class="btn-shine"></div>
        </button>
    </form>
</div>
