<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Avatar Upload') }}
        </h2>
    </x-slot>

    @push('styles')
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Subir Avatar</h3>
                    <form action="{{ route('media.store') }}" 
                          method="post" 
                          enctype="multipart/form-data" 
                          id="avatar-upload" 
                          class="dropzone border-2 border-dashed border-gray-300 rounded-lg">
                        @csrf
                        <div class="dz-message" data-dz-message>
                            <div class="text-lg font-medium text-gray-600">
                                Arrastra tu imagen aquí o haz clic para seleccionar
                            </div>
                            <div class="text-sm text-gray-500 mt-2">
                                Tipos permitidos: {{ implode(', ', config('media.avatar.allowed_types')) }}<br>
                                Tamaño máximo: {{ config('media.avatar.max_file_size')/1024 }}MB<br>
                                Dimensiones máximas: {{ config('media.avatar.max_dimensions') }}x{{ config('media.avatar.max_dimensions') }} píxeles
                            </div>
                        </div>
                        <input type="hidden" name="model_type" value="App\Models\User">
                        <input type="hidden" name="model_id" value="{{ auth()->id() }}">
                        <input type="hidden" name="collection" value="avatar">
                    </form>

                    <div class="mt-4 text-right">
                        <button type="button" 
                                id="submit-all" 
                                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Guardar Avatar
                        </button>
                    </div>
                </div>

                @if(auth()->user()->getFirstMediaUrl('avatar'))
                <div class="p-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Avatar Actual</h3>
                    <div class="flex flex-col items-center space-y-4">
                        <img src="{{ auth()->user()->getFirstMediaUrl('avatar', 'medium') }}" 
                             alt="Avatar actual" 
                             class="w-32 h-32 rounded-full object-cover" id="avatar-image">
                        <img src="{{ auth()->user()->getFirstMediaUrl('avatar', 'thumb') }}" 
                             alt="Avatar thumbnail" 
                             class="w-16 h-16 rounded-full object-cover">
                        @if($avatarMedia = auth()->user()->getFirstMedia('avatar'))
                        <form action="{{ route('media.destroy', $avatarMedia) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('¿Estás seguro de que quieres eliminar tu avatar?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500">
                                Eliminar Avatar
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>
    <script>
        const config = {
            maxFileSize: {{ config('media.avatar.max_file_size') }}, // KB
            maxDimensions: {{ config('media.avatar.max_dimensions') }},
            allowedTypes: {!! json_encode(config('media.avatar.allowed_types')) !!}
        };

        const dropzone = new Dropzone("#avatar-upload", {
            url: "{{ route('media.store') }}",
            paramName: "file",
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
            success: function(file, response) {
                if (response.success) {
                    const avatarImg = document.querySelector('#avatar-image');
                    if (avatarImg) {
                        avatarImg.src = response.url;
                    }
                }
            },
            error: function(file, errorMessage) {
                let message = '';
                if (typeof errorMessage === 'string') {
                    if (errorMessage.includes('dimensions')) {
                        message = `Dimensiones inválidas. Máximo ${config.maxDimensions}x${config.maxDimensions} píxeles`;
                    } else if (errorMessage.includes('file size')) {
                        message = `Tamaño máximo excedido. Límite: ${config.maxFileSize/1024}MB`;
                    } else if (errorMessage.includes('file type')) {
                        message = `Tipo de archivo no permitido. Tipos aceptados: ${config.allowedTypes.join(', ')}`;
                    } else {
                        message = errorMessage;
                    }
                } else if (errorMessage.error) {
                    message = errorMessage.error;
                } else {
                    message = 'Error al subir el archivo';
                }
                alert(message);
            }
        });
    </script>
    @endpush
</x-app-layout>
