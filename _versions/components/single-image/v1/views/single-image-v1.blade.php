<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('media.upload_title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="info-box mb-4">
                        <div class="text-sm text-gray-600">
                            {{ __('media.allowed_types') }}: {{ implode(', ', config('media.avatar.allowed_types')) }}<br>
                            {{ __('media.max_file_size') }}: {{ config('media.avatar.max_file_size')/1024 }}MB<br>
                            {{ __('media.max_dimensions') }}: {{ config('media.avatar.max_dimensions') }}x{{ config('media.avatar.max_dimensions') }}
                        </div>
                    </div>

                    <form id="mediaUploadForm" action="{{ route('media.store') }}" class="dropzone mb-4" method="POST">
                        @csrf
                        <input type="hidden" name="model_type" value="App\Models\User">
                        <input type="hidden" name="model_id" value="{{ auth()->id() }}">
                        <input type="hidden" name="collection" value="avatar">
                    </form>

                    <button type="button" id="uploadBtn" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('media.upload_button') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <style>
        .dropzone {
            border: 2px dashed #ccc !important;
            border-radius: 4px;
            padding: 20px;
            text-align: center;
            background: white;
            margin-bottom: 1rem;
        }
        .dropzone .dz-message {
            margin: 2em 0;
        }
        .dropzone .dz-preview .dz-image {
            border-radius: 4px;
        }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .message {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0.25rem;
            background-color: #fff;
            border: 1px solid;
        }
        .message.success {
            color: #0f5132;
            background-color: #d1e7dd;
            border-color: #badbcc;
        }
        .message.error {
            color: #842029;
            background-color: #f8d7da;
            border-color: #f5c2c7;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;

        const config = {
            maxFileSize: {{ config('media.avatar.max_file_size') }},
            maxDimensions: {{ config('media.avatar.max_dimensions') }},
            allowedTypes: {!! json_encode(config('media.avatar.allowed_types')) !!}
        };

        const dropzone = new Dropzone("#mediaUploadForm", {
            url: "{{ route('media.store') }}",
            maxFiles: 1,
            acceptedFiles: config.allowedTypes.map(type => `.${type}`).join(','),
            maxFilesize: config.maxFileSize / 1024,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            params: {
                model_type: "App\\Models\\User",
                model_id: {{ auth()->id() }},
                collection: "avatar"
            },
            autoProcessQueue: false,
            addRemoveLinks: true,
            dictRemoveFile: "{{ __('media.remove_file') }}",
            init: function() {
                const myDropzone = this;

                document.querySelector("#uploadBtn").addEventListener("click", function(e) {
                    e.preventDefault();
                    if (myDropzone.files.length > 0) {
                        myDropzone.processQueue();
                    } else {
                        showMessage('{{ __('media.select_image_first') }}', 'error');
                    }
                });
            },
            success: function(file, response) {
                if (response.success) {
                    showMessage('{{ __('media.upload_success') }}', 'success');
                    this.removeFile(file);
                }
            },
            error: function(file, errorMessage) {
                console.log('Error response:', errorMessage);
                let message;
                
                if (typeof errorMessage === 'object') {
                    // Si es un error de tamaño de archivo
                    if (errorMessage.error) {
                        message = errorMessage.error;
                    } else {
                        // Si es un error de validación del servidor
                        message = errorMessage.errors?.file?.[0] || 
                                errorMessage.message || 
                                '{{ __("media.upload_error") }}';
                    }
                } else {
                    message = errorMessage;
                }
                
                showMessage(message, 'error');
                this.removeFile(file);
            }
        });

        function showMessage(message, type) {
            // Eliminar mensajes anteriores
            const oldMessages = document.querySelectorAll('.message');
            oldMessages.forEach(msg => msg.remove());
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.textContent = message;
            
            const form = document.getElementById('mediaUploadForm');
            form.parentNode.insertBefore(messageDiv, form);
            
            setTimeout(() => messageDiv.remove(), 10000);
        }
    </script>
    @endpush
</x-app-layout>
