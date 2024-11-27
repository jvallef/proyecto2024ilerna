@php
// Copia de la implementación original del avatar uploader
// Versión 1 - Implementación básica con validación cliente/servidor
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Test Avatar Upload') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form id="avatarForm" method="POST" action="{{ route('media.avatar.store') }}" enctype="multipart/form-data" class="space-y-4">
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

                        <!-- Avatar -->
                        <div class="mt-4">
                            <x-input-label for="avatar" :value="__('Avatar')" />
                            <input type="file" 
                                   id="avatar" 
                                   name="avatar" 
                                   class="mt-1 block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-full file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-violet-50 file:text-violet-700
                                          hover:file:bg-violet-100"
                                   accept="image/{{ implode(',image/', $config['allowedTypes']) }}"
                                   required />
                            <p class="mt-1 text-sm text-gray-500">
                                Tamaño máximo permitido: {{ $config['maxSize'] }}KB
                                <br>
                                Tipos de archivo permitidos: {{ implode(', ', $config['allowedTypes']) }}
                            </p>
                            <p id="fileError" class="mt-2 text-sm text-red-600 hidden"></p>
                            <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                            @error('file')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Create User with Avatar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('avatarForm').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('avatar');
            const file = fileInput.files[0];
            const maxSize = {{ $config['maxSize'] * 1024 }}; // Convertir KB a bytes
            const errorElement = document.getElementById('fileError');
            const allowedTypes = {!! json_encode($config['allowedTypes']) !!};
            
            if (file) {
                // Validar tamaño
                if (file.size > maxSize) {
                    e.preventDefault();
                    errorElement.textContent = `El archivo es demasiado grande. El tamaño máximo permitido es ${maxSize / 1024}KB`;
                    errorElement.classList.remove('hidden');
                    fileInput.value = '';
                    return false;
                }

                // Validar tipo
                const fileType = file.type.split('/')[1];
                if (!allowedTypes.includes(fileType)) {
                    e.preventDefault();
                    errorElement.textContent = `Tipo de archivo no permitido. Los tipos permitidos son: ${allowedTypes.join(', ')}`;
                    errorElement.classList.remove('hidden');
                    fileInput.value = '';
                    return false;
                }
            }
            
            errorElement.classList.add('hidden');
            return true;
        });

        // Validar también cuando se selecciona el archivo
        document.getElementById('avatar').addEventListener('change', function(e) {
            const file = this.files[0];
            const maxSize = {{ $config['maxSize'] * 1024 }};
            const errorElement = document.getElementById('fileError');
            const allowedTypes = {!! json_encode($config['allowedTypes']) !!};
            
            if (file) {
                // Validar tamaño
                if (file.size > maxSize) {
                    errorElement.textContent = `El archivo es demasiado grande. El tamaño máximo permitido es ${maxSize / 1024}KB`;
                    errorElement.classList.remove('hidden');
                    this.value = '';
                    return;
                }

                // Validar tipo
                const fileType = file.type.split('/')[1];
                if (!allowedTypes.includes(fileType)) {
                    errorElement.textContent = `Tipo de archivo no permitido. Los tipos permitidos son: ${allowedTypes.join(', ')}`;
                    errorElement.classList.remove('hidden');
                    this.value = '';
                    return;
                }

                errorElement.classList.add('hidden');
            }
        });
    </script>
    @endpush
</x-app-layout>
