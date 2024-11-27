<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nuevo Usuario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Profile Image -->
                        <div class="mt-4">
                            <x-input-label for="profile_image" :value="__('Profile Image')" />
                            
                            <div class="info-box mb-4">
                                <div class="text-sm text-gray-600">
                                    {{ __('media.allowed_types') }}: {{ implode(', ', config('media.avatar.allowed_types')) }}<br>
                                    {{ __('media.max_file_size') }}: {{ config('media.avatar.max_file_size')/1024 }}MB<br>
                                    {{ __('media.max_dimensions') }}: {{ config('media.avatar.max_dimensions') }}x{{ config('media.avatar.max_dimensions') }}
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <!-- Zona de dropzone -->
                                <div class="col-span-1">
                                    <form id="mediaUploadForm" action="{{ route('media.store') }}" class="dropzone mb-4" method="POST" onsubmit="return false;">
                                        @csrf
                                        <input type="hidden" name="model_type" value="App\Models\User">
                                        <input type="hidden" name="model_id" value="{{ auth()->id() }}">
                                        <input type="hidden" name="collection" value="avatar">
                                    </form>
                                </div>

                                <!-- Panel de información -->
                                <div class="col-span-2">
                                    <!-- Info Panel -->
                                    <div id="imageInfoPanel" class="hidden mb-4 p-3 bg-gray-50 rounded-lg">
                                        <h3 class="font-semibold mb-2 text-sm">Información de la imagen</h3>
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <span class="text-xs">Nombre:</span>
                                                <span id="imageFileName" class="ml-2 text-xs"></span>
                                                <span id="imageFileNameCheck" class="ml-1"></span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-xs">Dimensiones:</span>
                                                <span id="imageDimensions" class="ml-2 text-xs"></span>
                                                <span id="imageDimensionsCheck" class="ml-1"></span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-xs">Tamaño:</span>
                                                <span id="imageSize" class="ml-2 text-xs"></span>
                                                <span id="imageSizeCheck" class="ml-1"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="uploadBtn" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('media.upload_button') }}
                            </button>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Register') }}
                            </x-primary-button>
                        </div>
                    </form>
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
        .check-icon {
            color: #198754;
        }
        .x-icon {
            color: #dc3545;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script src="{{ asset('js/components/media/MediaUploader.js') }}"></script>
    <script src="{{ asset('js/components/media/SingleImageUploader.js') }}"></script>
    <script>
        Dropzone.autoDiscover = false;

        const uploader = new SingleImageUploader({
            maxFileSize: {{ config('media.avatar.max_file_size') }},
            maxDimensions: {{ config('media.avatar.max_dimensions') }},
            allowedTypes: {!! json_encode(config('media.avatar.allowed_types')) !!},
            uploadUrl: "{{ route('media.store') }}",
            messages: {
                default: "{{ __('media.dropzone.message') }}",
                removeFile: "{{ __('media.remove_file') }}",
                invalidType: "{{ __('media.dropzone.invalid_type') }}",
                fileTooBig: "{{ __('media.dropzone.file_too_big') }}",
                maxFiles: "{{ __('media.dropzone.max_files') }}",
                selectFirst: "{{ __('media.select_image_first') }}",
                success: "{{ __('media.upload_success') }}",
                error: "{{ __('media.upload_error') }}"
            }
        });

        document.querySelector("#uploadBtn").addEventListener("click", (e) => {
            e.preventDefault();
            uploader.processUpload();
        });
    </script>
    @endpush
</x-app-layout>
