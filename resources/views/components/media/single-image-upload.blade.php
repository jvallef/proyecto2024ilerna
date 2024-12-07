@props([
    'name' => 'cover',
    'label' => __('Imagen'),
    'required' => false,
    'value' => null,
    'error' => null,
    'model' => null,
    'collection' => 'cover',
    'aspectRatio' => '16/9'
])

@php
    $config = [
        'maxSize' => config("media.{$collection}.max_file_size"),
        'allowedTypes' => config("media.{$collection}.allowed_types"),
        'maxDimensions' => config("media.{$collection}.max_dimensions")
    ];
    
    $hasImage = $model && $model->getFirstMediaUrl($collection);
    $imageUrl = $hasImage ? $model->getFirstMediaUrl($collection) : null;
@endphp

<div {{ $attributes->merge(['class' => 'mt-4']) }}>
    <x-input-label :for="$name" :value="$label" />
    
    @if($hasImage)
        <div class="mt-2 mb-4">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-48">
                    <div style="aspect-ratio: {{ $aspectRatio }};" class="relative overflow-hidden rounded-lg border border-gray-200">
                        <img src="{{ $imageUrl }}" 
                             alt="{{ __('Imagen actual') }}" 
                             class="absolute inset-0 w-full h-full object-cover">
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Imagen actual') }}</p>
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
        @if($hasImage)
            {{ __('Selecciona un nuevo archivo para cambiar la imagen actual') }}
            <br>
        @endif
        {{ __('Tamaño máximo permitido: :size KB', ['size' => $config['maxSize']]) }}
        <br>
        {{ __('Tipos de archivo permitidos: :types', ['types' => strtoupper(implode(', ', $config['allowedTypes']))]) }}
        <br>
        {{ __('Dimensiones máximas: :dimensions x :dimensions píxeles', ['dimensions' => $config['maxDimensions']]) }}
    </p>
    
    <p id="{{ $name }}Error" class="mt-2 text-sm text-red-600 hidden"></p>
    
    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

@once
    @push('scripts')
    <script>
        class ImageUploader {
            constructor(inputId) {
                this.input = document.getElementById(inputId);
                this.error = document.getElementById(inputId + 'Error');
                this.maxSize = {{ $config['maxSize'] * 1024 }};
                this.maxDimensions = {{ $config['maxDimensions'] }};
                this.allowedTypes = {!! json_encode($config['allowedTypes']) !!};
                this.messages = {
                    fileTooLarge: '{{ __("El archivo es demasiado grande. El tamaño máximo permitido es :size KB", ["size" => $config["maxSize"]]) }}',
                    invalidType: '{{ __("Tipo de archivo no permitido. Los tipos permitidos son: :types", ["types" => implode(", ", $config["allowedTypes"])]) }}',
                    dimensionsTooLarge: '{{ __("La imagen es demasiado grande. Las dimensiones máximas permitidas son :dimensions x :dimensions píxeles", ["dimensions" => $config["maxDimensions"]]) }}'
                };
                
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
                
                // Validar dimensiones de la imagen
                const img = new Image();
                img.src = URL.createObjectURL(file);
                
                img.onload = () => {
                    URL.revokeObjectURL(img.src);
                    if (!this.validateDimensions(img)) {
                        this.input.value = '';
                        return false;
                    }
                };
                
                this.error.classList.add('hidden');
                return true;
            }
            
            validateFileSize(file) {
                if (file.size > this.maxSize) {
                    this.showError(this.messages.fileTooLarge);
                    return false;
                }
                return true;
            }
            
            validateFileType(file) {
                const fileType = file.type.split('/')[1];
                if (!this.allowedTypes.includes(fileType)) {
                    this.showError(this.messages.invalidType);
                    return false;
                }
                return true;
            }
            
            validateDimensions(img) {
                if (img.width > this.maxDimensions || img.height > this.maxDimensions) {
                    this.showError(this.messages.dimensionsTooLarge);
                    return false;
                }
                return true;
            }
            
            validateOnSubmit(event) {
                if (!this.validateFile(event)) {
                    event.preventDefault();
                }
            }
            
            showError(message) {
                this.error.textContent = message;
                this.error.classList.remove('hidden');
                this.input.value = '';
            }
        }
        
        // Inicializar el uploader
        new ImageUploader('{{ $name }}');
    </script>
    @endpush
@endonce
