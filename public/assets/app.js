document.addEventListener('DOMContentLoaded', () => {
    const dropArea = document.getElementById('dropArea');
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const submitBtn = document.getElementById('submitBtn');
    const removeBtn = document.getElementById('removeFile');
    const uploadForm = document.getElementById('uploadForm');

    // Drag & Drop Events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        dropArea.classList.add('highlight');
    }

    function unhighlight() {
        dropArea.classList.remove('highlight');
    }

    // Handle Drop
    dropArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    // Handle Browse Click
    dropArea.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        handleFiles(this.files);
    });

    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            validateAndPreview(file);
        }
    }

    function validateAndPreview(file) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain'];

        // Reset
        submitBtn.disabled = true;

        // Validation
        if (file.size > maxSize) {
            alert('File is too large (Max 5MB)');
            return;
        }

        if (!allowedTypes.includes(file.type)) {
            alert('Invalid file type. Allowed: JPG, PNG, GIF, PDF, TXT');
            return;
        }

        // Update UI
        dropArea.classList.add('hidden');
        filePreview.classList.remove('hidden');
        submitBtn.disabled = false;

        // File Info
        filePreview.querySelector('.file-name').textContent = file.name;
        filePreview.querySelector('.file-size').textContent = formatBytes(file.size);

        // Thumbnail
        const iconDiv = filePreview.querySelector('.preview-icon');
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = function () {
                iconDiv.style.backgroundImage = `url(${reader.result})`;
                iconDiv.textContent = '';
            }
        } else {
            iconDiv.style.backgroundImage = 'none';
            iconDiv.textContent = '📄';
            iconDiv.style.display = 'flex';
            iconDiv.style.justifyContent = 'center';
            iconDiv.style.alignItems = 'center';
            iconDiv.style.fontSize = '24px';
        }

        // Sync with input (if drag dropped)
        // Note: You can't programmatically set file input files for security, 
        // so if dropped, we might need to use FormData on submit or just accept it's mostly visual if using standard form post.
        // However, for standard form POST work with DragDrop, we usually need AJAX.
        // To keep it simple and working with the PHP POST architecture we built:
        // We will assume 'browse' is primary for layout, OR we use DataTransfer to set input files (modern browsers allow this).

        if (fileInput.files.length === 0 || fileInput.files[0] !== file) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
        }
    }

    // Remove File
    removeBtn.addEventListener('click', (e) => {
        e.preventDefault(); // Prevent form submit if button inside form
        fileInput.value = '';
        dropArea.classList.remove('hidden');
        filePreview.classList.add('hidden');
        submitBtn.disabled = true;
    });

    // Valid Utils
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    // Loading State
    uploadForm.addEventListener('submit', () => {
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Uploading... <div class="btn-shine"></div>'; // Simple feedback
    });
});
