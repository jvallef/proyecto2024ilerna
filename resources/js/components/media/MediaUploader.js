class MediaUploader {
    constructor(options = {}) {
        this.config = {
            maxFileSize: options.maxFileSize || 2048,
            maxDimensions: options.maxDimensions || 2048,
            allowedTypes: options.allowedTypes || ['jpg', 'jpeg', 'png', 'webp'],
            messages: options.messages || {},
            selector: options.selector || '#mediaUploadForm',
            messageTimeout: options.messageTimeout || 10000,
            uploadUrl: options.uploadUrl || '/upload',
            hooks: options.hooks || {},
            params: options.params || {}
        };

        this.eventListeners = new Map();
        this.dropzone = null;
        this.initDropzone();
    }

    initDropzone() {
        // Llamar al hook beforeInit si existe
        if (this.config.hooks.beforeInit) {
            this.config.hooks.beforeInit(this);
        }

        this.dropzone = new Dropzone(this.config.selector, {
            url: this.config.uploadUrl,
            maxFiles: 1,
            acceptedFiles: this.config.allowedTypes.map(type => `.${type}`).join(','),
            maxFilesize: this.config.maxFileSize / 1024,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            params: this.config.params,
            autoProcessQueue: false,
            addRemoveLinks: true,
            dictDefaultMessage: this.config.messages.default || "Drop files here to upload",
            dictRemoveFile: this.config.messages.removeFile || "Remove file",
            dictInvalidFileType: this.config.messages.invalidType || "You can't upload files of this type",
            dictFileTooBig: this.config.messages.fileTooBig || "File is too big",
            dictMaxFilesExceeded: this.config.messages.maxFiles || "You can't upload any more files",
            // Añadir animaciones suaves
            createImageThumbnails: true,
            thumbnailWidth: 120,
            thumbnailHeight: 120,
            previewTemplate: this.getPreviewTemplate()
        });

        this.setupEventListeners();

        // Llamar al hook afterInit si existe
        if (this.config.hooks.afterInit) {
            this.config.hooks.afterInit(this);
        }
    }

    getPreviewTemplate() {
        return `
        <div class="dz-preview dz-file-preview transition-all duration-300 ease-in-out">
            <div class="dz-image">
                <img data-dz-thumbnail />
            </div>
            <div class="dz-details">
                <div class="dz-filename"><span data-dz-name></span></div>
                <div class="dz-size" data-dz-size></div>
            </div>
            <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
            <div class="dz-success-mark"><span>✔</span></div>
            <div class="dz-error-mark"><span>✘</span></div>
            <div class="dz-error-message"><span data-dz-errormessage></span></div>
        </div>`;
    }

    setupEventListeners() {
        // Eventos base de Dropzone
        const events = ['addedfile', 'removedfile', 'success', 'error', 'uploadprogress'];
        
        events.forEach(event => {
            this.dropzone.on(event, (...args) => {
                // Emitir evento personalizado
                this.emit(event, ...args);
                
                // Manejar evento específico
                switch(event) {
                    case 'addedfile':
                        if (this.config.hooks.beforeValidate) {
                            this.config.hooks.beforeValidate(args[0]);
                        }
                        break;
                    case 'success':
                        if (this.config.hooks.afterUpload) {
                            this.config.hooks.afterUpload(args[1]);
                        }
                        this.showMessage(this.config.messages.success || 'File uploaded successfully', 'success');
                        break;
                    case 'error':
                        if (this.config.hooks.onError) {
                            this.config.hooks.onError(args[1]);
                        }
                        const message = this.getErrorMessage(args[1]);
                        this.showMessage(message, 'error');
                        break;
                    case 'uploadprogress':
                        this.updateProgress(args[1]);
                        break;
                }
            });
        });
    }

    on(event, callback) {
        if (!this.eventListeners.has(event)) {
            this.eventListeners.set(event, []);
        }
        this.eventListeners.get(event).push(callback);
    }

    emit(event, ...args) {
        const listeners = this.eventListeners.get(event) || [];
        listeners.forEach(callback => callback(...args));
    }

    updateProgress(progress) {
        // Implementación base para la barra de progreso
        const progressBar = document.querySelector(`${this.config.selector} .dz-upload`);
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
            progressBar.style.transition = 'width 0.3s ease-in-out';
        }
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
        oldMessages.forEach(msg => {
            msg.classList.add('opacity-0');
            setTimeout(() => msg.remove(), 300);
        });
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type} opacity-0 transition-opacity duration-300`;
        messageDiv.textContent = message;
        
        const form = document.querySelector(this.config.selector);
        form.parentNode.insertBefore(messageDiv, form);
        
        // Forzar reflow para que la transición funcione
        messageDiv.offsetHeight;
        messageDiv.classList.remove('opacity-0');
        
        setTimeout(() => {
            messageDiv.classList.add('opacity-0');
            setTimeout(() => messageDiv.remove(), 300);
        }, this.config.messageTimeout - 300);
    }

    processUpload() {
        if (this.dropzone.files.length > 0) {
            if (this.config.hooks.beforeUpload && !this.config.hooks.beforeUpload(this.dropzone.files[0])) {
                return;
            }
            this.dropzone.processQueue();
        } else {
            this.showMessage(this.config.messages.selectFirst || 'Please select a file first', 'error');
        }
    }

    destroy() {
        if (this.config.hooks.beforeDestroy) {
            this.config.hooks.beforeDestroy(this);
        }
        this.dropzone.destroy();
        this.eventListeners.clear();
    }
}
