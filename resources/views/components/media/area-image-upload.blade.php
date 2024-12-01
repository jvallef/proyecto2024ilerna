@props([
    'name' => 'image',
    'label' => 'Imagen del área',
    'required' => false,
    'value' => null,
    'error' => null,
    'model' => null
])

@php
    $allowedTypes = env('AREA_IMAGE_ALLOWED_TYPES', 'jpg,jpeg,png,webp');
    $config = [
        'maxSize' => env('AREA_IMAGE_MAX_FILE_SIZE', 2048),
        'allowedTypes' => explode(',', $allowedTypes),
        'maxDimensions' => env('AREA_IMAGE_MAX_DIMENSIONS', 2048)
    ];
    
    $hasImage = $model && $model->getFirstMediaUrl('areas');
    $imageUrl = $hasImage ? $model->getFirstMediaUrl('areas') : null;
@endphp

<div x-data="areaImageUpload({
        maxSize: {{ $config['maxSize'] * 1024 }},
        allowedTypes: {{ json_encode($config['allowedTypes']) }},
        initialUrl: '{{ $imageUrl }}'
    })" 
    {{ $attributes->merge(['class' => 'mt-4']) }}>
    
    <x-input-label :for="$name" :value="$label" />
    
    <div class="mt-2 mb-4" x-show="imageUrl">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <img :src="imageUrl" 
                     alt="Imagen actual" 
                     class="h-16 w-16 rounded-lg object-cover border border-gray-200">
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Imagen actual</p>
                <button type="button" 
                        @click="removeImage"
                        class="mt-1 text-sm text-red-600 hover:text-red-800">
                    Eliminar imagen
                </button>
            </div>
        </div>
    </div>
    
    <input type="file" 
           id="{{ $name }}" 
           name="{{ $name }}" 
           @change="handleFileSelect"
           class="mt-1 block w-full text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-full file:border-0
                  file:text-sm file:font-semibold
                  file:bg-violet-50 file:text-violet-700
                  hover:file:bg-violet-100"
           accept="image/{{ implode(',image/', $config['allowedTypes']) }}"
           {{ $required ? 'required' : '' }} />
    
    <p class="mt-1 text-sm text-gray-500">
        <template x-if="imageUrl">
            <span>
                Selecciona un nuevo archivo para cambiar la imagen actual<br>
            </span>
        </template>
        Tamaño máximo permitido: {{ $config['maxSize'] }}KB<br>
        Tipos de archivo permitidos: {{ implode(', ', $config['allowedTypes']) }}
    </p>
    
    <p x-show="error" 
       x-text="error"
       class="mt-2 text-sm text-red-600"></p>
    
    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

@push('scripts')
<script>
function areaImageUpload(config) {
    return {
        imageUrl: config.initialUrl,
        error: '',
        maxSize: config.maxSize,
        allowedTypes: config.allowedTypes,
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (!this.validateFileSize(file)) return;
            if (!this.validateFileType(file)) return;
            
            this.error = '';
            this.readFile(file);
        },
        
        validateFileSize(file) {
            if (file.size > this.maxSize) {
                this.error = `El archivo es demasiado grande. El tamaño máximo permitido es ${this.maxSize / 1024}KB`;
                this.resetInput();
                return false;
            }
            return true;
        },
        
        validateFileType(file) {
            const fileType = file.type.split('/')[1];
            if (!this.allowedTypes.includes(fileType)) {
                this.error = `Tipo de archivo no permitido. Los tipos permitidos son: ${this.allowedTypes.join(', ')}`;
                this.resetInput();
                return false;
            }
            return true;
        },
        
        readFile(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imageUrl = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        
        removeImage() {
            this.imageUrl = '';
            this.error = '';
            this.resetInput();
        },
        
        resetInput() {
            const input = this.$el.querySelector('input[type="file"]');
            if (input) input.value = '';
        }
    }
}
</script>
@endpush
