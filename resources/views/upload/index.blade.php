<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload Test') }}
        </h2>
    </x-slot>

    @push('styles')
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
      .dz-preview .dz-image img{
        width: 100% !important;
        height: 100% !important;
        object-fit: cover;
      }
    </style>
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('upload.store') }}" 
                          method="post" 
                          enctype="multipart/form-data" 
                          id="image-upload" 
                          class="dropzone border-2 border-dashed border-gray-300 rounded-lg p-6">
                        @csrf
                        <div class="text-center">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Arrastra imágenes aquí o haz clic para seleccionar</h4>
                        </div>
                    </form>

                    <button id="uploadFile" style="background-color: #4f46e5;" class="mt-4 px-6 py-2 text-white font-semibold rounded-lg shadow-md hover:opacity-90">
                        Subir Imágenes
                    </button>

                    <!-- Preview de imágenes subidas -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Imágenes subidas:</h3>
                        <div id="preview" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($images as $image)
                            <div class="relative group w-48 h-48">
                                <img src="{{ asset($image->path) }}" alt="{{ $image->name }}" class="w-full h-full object-cover rounded-lg shadow-md">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 rounded-lg flex items-center justify-center">
                                    <span class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        {{ $image->name }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>
    <script type="text/javascript">
        Dropzone.autoDiscover = false;

        // Limpiar el preview al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('preview').innerHTML = '';
        });

        var myDropzone = new Dropzone("#image-upload", {
            init: function() {
                myDropzone = this;

                this.on("success", function(file, response) {
                    // Añadir las imágenes al grid de preview
                    var preview = document.getElementById('preview');
                    
                    response.images.forEach(function(image) {
                        var div = document.createElement('div');
                        div.className = 'relative group w-48 h-48';
                        div.innerHTML = `
                            <img src="${image.path}" alt="${image.name}" class="w-full h-full object-cover rounded-lg shadow-md">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 rounded-lg flex items-center justify-center">
                                <span class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    ${image.name}
                                </span>
                            </div>
                        `;
                        preview.appendChild(div);
                    });
                    
                    // Limpiar el dropzone después de subir
                    this.removeAllFiles();
                });
            },
            autoProcessQueue: false,
            paramName: "files",
            uploadMultiple: true,
            maxFilesize: 5,
            acceptedFiles: ".jpeg,.jpg,.png,.gif",
            addRemoveLinks: true,
            dictDefaultMessage: "Arrastra imágenes aquí o haz clic para seleccionar",
            dictFileTooBig: "El archivo es demasiado grande. Tamaño máximo: 5MB",
            dictInvalidFileType: "No puedes subir archivos de este tipo",
            dictResponseError: "Error al subir el archivo",
            dictCancelUpload: "Cancelar subida",
            dictUploadCanceled: "Subida cancelada",
            dictRemoveFile: "Eliminar archivo"
        });

        $('#uploadFile').click(function(){
            if (myDropzone.getQueuedFiles().length > 0) {
                myDropzone.processQueue();
            } else {
                alert('Por favor, selecciona al menos un archivo para subir.');
            }
        });
    </script>
    @endpush
</x-app-layout>
