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
                            
                            <!-- Info text -->
                            <div class="text-xs text-gray-600 mb-2">
                                {{ __('media.allowed_types') }}: jpg, jpeg, png, webp.
                                {{ __('media.max_file_size') }}: {{ config('media.avatar.max_file_size')/1024 }}MB.
                                {{ __('media.max_dimensions') }}: {{ config('media.avatar.max_dimensions') }}x{{ config('media.avatar.max_dimensions') }}
                            </div>

                            <!-- Upload container -->
                            <div class="grid grid-cols-12 gap-4 items-start">
                                <!-- Dropzone area -->
                                <div class="col-span-4">
                                    <div id="mediaUploadForm" class="dropzone" style="width: 200px; height: 200px;">
                                        <div class="dz-message" data-dz-message>
                                            <span class="text-xs">{{ __('Arrastra una imagen o haz click') }}</span>
                                        </div>
                                    </div>
                                    <button type="button" id="uploadBtn" style="margin-top: 1rem; width: 200px; padding: 0.5rem 1rem; background-color: #4f46e5; color: white; border-radius: 0.375rem; font-size: 0.875rem; line-height: 1.25rem;">
                                        {{ __('Upload Image') }}
                                    </button>
                                </div>

                                <!-- Info panel -->
                                <div class="col-span-8">
                                    <div id="imageInfoPanel" class="hidden">
                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between">
                                                <span id="imageFileName" class="text-xs truncate"></span>
                                                <span id="imageFileNameCheck" class="ml-1 flex-shrink-0"></span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span id="imageDimensions" class="text-xs truncate"></span>
                                                <span id="imageDimensionsCheck" class="ml-1 flex-shrink-0"></span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span id="imageSize" class="text-xs truncate"></span>
                                                <span id="imageSizeCheck" class="ml-1 flex-shrink-0"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
            padding: 10px;
            text-align: center;
            background: white;
            margin-bottom: 0.5rem;
            min-height: auto !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dropzone:hover {
            border-color: #818cf8 !important;
        }
        .dropzone .dz-message {
            margin: 0 !important;
            font-size: 0.875rem;
        }
        .dropzone .dz-preview {
            min-height: auto;
            margin: 0;
        }
        .dropzone .dz-preview .dz-image {
            border-radius: 4px;
            width: 60px;
            height: 60px;
        }
        .dropzone .dz-preview.dz-image-preview {
            background: transparent;
        }
        .message {
            padding: 0.5rem;
            margin: 0.5rem 0;
            border-radius: 0.25rem;
            background-color: #fff;
            border: 1px solid;
            font-size: 0.875rem;
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
            width: 1rem;
            height: 1rem;
        }
        .x-icon {
            color: #dc3545;
            width: 1rem;
            height: 1rem;
        }
        #imageInfoPanel {
            font-size: 0.75rem;
            line-height: 1rem;
            background-color: #f9fafb;
            border-radius: 0.375rem;
            padding: 0.5rem;
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
    </script>
    @endpush
</x-app-layout>
