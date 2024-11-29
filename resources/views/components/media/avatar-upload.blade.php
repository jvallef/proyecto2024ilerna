@props([
    'name' => 'avatar',
    'label' => 'Avatar',
    'required' => false,
    'value' => null,
    'error' => null,
    'model' => null
])

@php
    $allowedTypes = env('AVATAR_ALLOWED_TYPES', 'jpg,jpeg,png,webp');
    $config = [
        'maxSize' => env('AVATAR_MAX_FILE_SIZE', 2048),
        'allowedTypes' => explode(',', $allowedTypes),
        'maxDimensions' => env('AVATAR_MAX_DIMENSIONS', 2048)
    ];
    
    $hasAvatar = $model && $model->getFirstMediaUrl('avatar');
    $avatarUrl = $hasAvatar ? $model->getFirstMediaUrl('avatar') : null;
@endphp

<div {{ $attributes->merge(['class' => 'mt-4']) }}>
    <x-input-label :for="$name" :value="$label" />
    
    @if($hasAvatar)
        <div class="mt-2 mb-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <img src="{{ $avatarUrl }}" alt="Avatar actual" class="h-16 w-16 rounded-full object-cover border border-gray-200">
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Avatar actual</p>
                </div>
            </div>
        </div>
    @endif
    
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
        @if($hasAvatar)
            Selecciona un nuevo archivo para cambiar el avatar actual
            <br>
        @endif
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
                this.config = @json($config);
                
                if (this.input) {
                    this.input.addEventListener('change', this.validateFile.bind(this));
                }
            }
            
            validateFile(event) {
                const file = event.target.files[0];
                this.error.classList.add('hidden');
                this.error.textContent = '';
                
                if (!file) return;
                
                // Validar tamaño
                if (file.size > this.config.maxSize * 1024) {
                    this.showError(`El archivo es demasiado grande. El tamaño máximo permitido es ${this.config.maxSize}KB`);
                    return false;
                }
                
                // Validar tipo
                const fileType = file.type.split('/')[1];
                if (!this.config.allowedTypes.includes(fileType)) {
                    this.showError(`Tipo de archivo no permitido. Use: ${this.config.allowedTypes.join(', ')}`);
                    return false;
                }
                
                // Validar dimensiones
                const img = new Image();
                img.src = URL.createObjectURL(file);
                
                img.onload = () => {
                    if (img.width > this.config.maxDimensions || img.height > this.config.maxDimensions) {
                        this.showError(`La imagen es demasiado grande. Las dimensiones máximas permitidas son ${this.config.maxDimensions}x${this.config.maxDimensions}px`);
                        this.input.value = '';
                    }
                    URL.revokeObjectURL(img.src);
                };
                
                return true;
            }
            
            showError(message) {
                this.error.textContent = message;
                this.error.classList.remove('hidden');
                this.input.value = '';
            }
        }
        
        // Inicializar para cada input de avatar en la página
        document.addEventListener('DOMContentLoaded', () => {
            new AvatarUploader('{{ $name }}');
        });
    </script>
    @endpush
@endonce
