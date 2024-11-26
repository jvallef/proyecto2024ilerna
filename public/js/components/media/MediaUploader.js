class MediaUploader {
    constructor(options = {}) {
        this.config = {
            maxFileSize: options.maxFileSize || 2048,
            maxDimensions: options.maxDimensions || 2048,
            allowedTypes: options.allowedTypes || ['jpg', 'jpeg', 'png', 'webp'],
            messages: options.messages || {},
            selector: options.selector || '#mediaUploadForm',
            messageTimeout: options.messageTimeout || 10000
        };

        this.dropzone = null;
        this.initDropzone();
    }

    initDropzone() {
        this.dropzone = new Dropzone(this.config.selector, {
            url: this.config.uploadUrl,
            maxFiles: 1,
            acceptedFiles: this.config.allowedTypes.map(type => `.${type}`).join(','),
            maxFilesize: this.config.maxFileSize / 1024,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            autoProcessQueue: false,
            addRemoveLinks: true,
            dictDefaultMessage: this.config.messages.default || "Drop files here to upload",
            dictRemoveFile: this.config.messages.removeFile || "Remove file",
            dictInvalidFileType: this.config.messages.invalidType || "You can't upload files of this type",
            dictFileTooBig: this.config.messages.fileTooBig || "File is too big",
            dictMaxFilesExceeded: this.config.messages.maxFiles || "You can't upload any more files"
        });

        this.setupEventListeners();
    }

    setupEventListeners() {
        this.dropzone.on("success", (file, response) => {
            if (response.success) {
                this.showMessage(this.config.messages.success || 'File uploaded successfully', 'success');
                this.dropzone.removeFile(file);
            }
        });

        this.dropzone.on("error", (file, errorMessage) => {
            console.log('Error response:', errorMessage);
            let message = this.getErrorMessage(errorMessage);
            this.showMessage(message, 'error');
            this.dropzone.removeFile(file);
        });
    }

    getErrorMessage(errorMessage) {
        if (typeof errorMessage === 'object') {
            if (errorMessage.error) {
                return errorMessage.error;
            }
            return errorMessage.errors?.file?.[0] || 
                   errorMessage.message || 
                   this.config.messages.error || 
                   'Error uploading file';
        }
        return errorMessage;
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    showMessage(message, type) {
        const oldMessages = document.querySelectorAll('.message');
        oldMessages.forEach(msg => msg.remove());
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.textContent = message;
        
        const form = document.querySelector(this.config.selector);
        form.parentNode.insertBefore(messageDiv, form);
        
        setTimeout(() => messageDiv.remove(), this.config.messageTimeout);
    }

    processUpload() {
        if (this.dropzone.files.length > 0) {
            this.dropzone.processQueue();
        } else {
            this.showMessage(this.config.messages.selectFirst || 'Please select a file first', 'error');
        }
    }
}
