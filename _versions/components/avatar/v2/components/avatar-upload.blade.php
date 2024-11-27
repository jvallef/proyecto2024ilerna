@props([
    'name' => 'avatar',
    'label' => 'Avatar',
    'required' => true,
    'value' => null,
    'error' => null
])

@php
    $allowedTypes = env('AVATAR_ALLOWED_TYPES', 'jpg,jpeg,png,webp');
    $config = [
        'maxSize' => env('AVATAR_MAX_FILE_SIZE', 2048),
        'allowedTypes' => explode(',', $allowedTypes),
        'maxDimensions' => env('AVATAR_MAX_DIMENSIONS', 2048)
    ];
@endphp

<div {{ $attributes->merge(['class' => 'mt-4']) }}>
    <x-input-label :for="$name" :value="$label" />
    <input type="file" 
           id="{{ $name }}" 
           name="{{ $name }}" 
           class="mt-1 block w-full text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-full file:border-0
                  file:text-sm file:font-semibold
                  file:bg-violet-50 file:text-violet-700
                  hover:file:bg-violet-100"
           accept="image/{{ implode(',image/', $config['allowedTypes']) }}"
           {{ $required ? 'required' : '' }} />
    
    <p class="mt-1 text-sm text-gray-500">
        Tamaño máximo permitido: {{ $config['maxSize'] }}KB
        <br>
        Tipos de archivo permitidos: {{ implode(', ', $config['allowedTypes']) }}
    </p>
    
    <p id="{{ $name }}Error" class="mt-2 text-sm text-red-600 hidden"></p>
    
    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

@once
    @push('scripts')
    <script>
        class AvatarUploader {
            constructor(inputId) {
                this.input = document.getElementById(inputId);
                this.error = document.getElementById(inputId + 'Error');
                this.maxSize = {{ $config['maxSize'] * 1024 }};
                this.allowedTypes = {!! json_encode($config['allowedTypes']) !!};
                
                this.setupEventListeners();
            }
            
            setupEventListeners() {
                this.input.addEventListener('change', this.validateFile.bind(this));
                this.input.closest('form')?.addEventListener('submit', this.validateOnSubmit.bind(this));
            }
            
            validateFile(event) {
                const file = this.input.files[0];
                if (!file) return true;
                
                if (!this.validateFileSize(file)) return false;
                if (!this.validateFileType(file)) return false;
                
                this.error.classList.add('hidden');
                return true;
            }
            
            validateFileSize(file) {
                if (file.size > this.maxSize) {
                    this.showError(`El archivo es demasiado grande. El tamaño máximo permitido es ${this.maxSize / 1024}KB`);
                    return false;
                }
                return true;
            }
            
            validateFileType(file) {
                const fileType = file.type.split('/')[1];
                if (!this.allowedTypes.includes(fileType)) {
                    this.showError(`Tipo de archivo no permitido. Los tipos permitidos son: ${this.allowedTypes.join(', ')}`);
                    return false;
                }
                return true;
            }
            
            validateOnSubmit(event) {
                if (!this.validateFile(event)) {
                    event.preventDefault();
                    return false;
                }
                return true;
            }
            
            showError(message) {
                this.error.textContent = message;
                this.error.classList.remove('hidden');
                this.input.value = '';
            }
        }

        // Inicializar el uploader
        new AvatarUploader('{{ $name }}');
    </script>
    @endpush
@endonce
