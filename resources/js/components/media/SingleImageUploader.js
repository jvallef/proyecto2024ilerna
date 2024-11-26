class SingleImageUploader extends MediaUploader {
    constructor(options = {}) {
        super(options);
        this.infoPanel = options.infoPanelSelector || '#imageInfoPanel';
        this.setupImageInfo();
    }

    setupImageInfo() {
        this.dropzone.on("addedfile", file => {
            this.updateImageInfo(file);
        });

        this.dropzone.on("removedfile", () => {
            document.querySelector(this.infoPanel).classList.add('hidden');
        });
    }

    getCheckIcon(isValid) {
        return isValid ? 
            '<svg class="w-4 h-4 check-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>' : 
            '<svg class="w-4 h-4 x-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>';
    }

    updateImageInfo(file) {
        const panel = document.querySelector(this.infoPanel);
        panel.classList.remove('hidden');

        // Nombre del archivo
        const fileName = document.getElementById('imageFileName');
        const fileNameCheck = document.getElementById('imageFileNameCheck');
        fileName.textContent = file.name;
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const isValidType = this.config.allowedTypes.includes(fileExtension);
        fileNameCheck.innerHTML = this.getCheckIcon(isValidType);

        // Tamaño del archivo
        const size = document.getElementById('imageSize');
        const sizeCheck = document.getElementById('imageSizeCheck');
        const formattedSize = this.formatFileSize(file.size);
        size.textContent = formattedSize;
        const isValidSize = file.size <= this.config.maxFileSize * 1024;
        sizeCheck.innerHTML = this.getCheckIcon(isValidSize);

        // Dimensiones
        const img = new Image();
        img.onload = () => {
            const dimensions = document.getElementById('imageDimensions');
            const dimensionsCheck = document.getElementById('imageDimensionsCheck');
            dimensions.textContent = `${img.width}x${img.height}`;
            const isValidDimensions = img.width <= this.config.maxDimensions && img.height <= this.config.maxDimensions;
            dimensionsCheck.innerHTML = this.getCheckIcon(isValidDimensions);
        };
        img.src = URL.createObjectURL(file);
    }
}
