<!-- Formulario -->
<form method="POST" 
    action="{{ isset($area) && $area->id ? route('admin.areas.update', $area) : route('admin.areas.store') }}" 
    enctype="multipart/form-data" 
    class="space-y-6">
    @csrf
    @if(isset($area) && $area->id)
        @method('PUT')
    @endif

    <!-- Información Básica -->
    <div class="border-b border-gray-200 pb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información básica</h3>
        
        <!-- Primera fila: Nombre y Área padre -->
        <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Nombre -->
            <div>
                <div class="flex items-center">
                    <x-input-label for="name" :value="__('Nombre')" />
                    <x-required-mark />
                </div>
                <x-text-input id="name" 
                         name="name" 
                         type="text" 
                         class="mt-1 block w-full" 
                         :value="old('name', $area->name ?? '')" 
                         required 
                         autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Área Padre -->
            <div>
                <x-input-label for="parent_id" :value="__('Área Padre')" />
                <select id="parent_id" 
                        name="parent_id"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">Selecciona un área padre</option>
                    @foreach($areasList as $areaId => $areaName)
                        <option value="{{ $areaId }}" 
                            {{ old('parent_id', isset($area) ? $area->parent_id : '') == $areaId ? 'selected' : '' }}>
                            {!! $areaName !!}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
            </div>
        </div>

        <!-- Segunda fila: Descripción y Estado -->
        <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Descripción -->
            <div>
                <x-input-label for="description" :value="__('Descripción')" />
                <textarea id="description" 
                          name="description" 
                          rows="3" 
                          class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $area->description ?? '') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <!-- Estado -->
            <div>
                <div class="flex items-center">
                    <x-input-label for="status" :value="__('Estado')" />
                    <x-required-mark />
                </div>
                <select id="status" 
                        name="status" 
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required>
                    <option value="draft" {{ old('status', $area->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Borrador</option>
                    <option value="published" {{ old('status', $area->status ?? '') === 'published' ? 'selected' : '' }}>Publicado</option>
                    <option value="suspended" {{ old('status', $area->status ?? '') === 'suspended' ? 'selected' : '' }}>Suspendido</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>
        </div>
    </div>

    <!-- Destacado -->
    <div class="flex items-center">
        <input type="checkbox" 
               id="featured" 
               name="featured" 
               value="1" 
               {{ old('featured', $area->featured ?? false) ? 'checked' : '' }}
               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
        <x-input-label for="featured" :value="__('Destacar esta área')" class="ml-2" />
        <x-input-error :messages="$errors->get('featured')" class="mt-2" />
    </div>

    <!-- Imagen -->
    <div>
        <x-media.single-image-upload
            :model="isset($area) ? $area : null"
            name="cover"
            :label="__('Imagen de Portada')"
            collection="cover"
            :required="false"
        />
    </div>

    <!-- SEO Metadatos -->
    <div class="border-b border-gray-200 pb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Metadatos SEO</h3>
        
        <!-- Primera fila: Título SEO y Keywords -->
        <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- SEO Título -->
            <div>
                <x-input-label for="meta_title" :value="__('Título SEO')" />
                <x-text-input id="meta_title" 
                             name="meta[title]" 
                             type="text" 
                             class="mt-1 block w-full" 
                             :value="old('meta.title', $area->meta['title'] ?? '')" 
                             maxlength="60" />
                <p class="mt-1 text-sm text-gray-500">Máximo 60 caracteres</p>
                <x-input-error :messages="$errors->get('meta.title')" class="mt-2" />
            </div>

            <!-- SEO Keywords -->
            <div>
                <x-input-label for="meta_keywords" :value="__('Palabras clave')" />
                <x-text-input id="meta_keywords" 
                             name="meta[keywords]" 
                             type="text" 
                             class="mt-1 block w-full" 
                             :value="old('meta.keywords', $area->meta['keywords'] ?? '')" />
                <p class="mt-1 text-sm text-gray-500">Separadas por comas</p>
                <x-input-error :messages="$errors->get('meta.keywords')" class="mt-2" />
            </div>
        </div>

        <!-- Segunda fila: Descripción SEO (ancho reducido) -->
        <div class="mt-4">
            <div class="sm:col-span-1 max-w-md">
                <x-input-label for="meta_description" :value="__('Descripción SEO')" />
                <textarea id="meta_description" 
                          name="meta[description]" 
                          rows="3"
                          maxlength="160" 
                          class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('meta.description', $area->meta['description'] ?? '') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Máximo 160 caracteres</p>
                <x-input-error :messages="$errors->get('meta.description')" class="mt-2" />
            </div>
        </div>
    </div>

    <!-- Botones -->
    <div class="flex justify-end space-x-4 border-t pt-4">
        <x-secondary-button onclick="window.history.back()">
            {{ __('Cancelar') }}
        </x-secondary-button>
        <x-primary-button>
            {{ isset($area) && $area->id ? __('Actualizar') : __('Crear') }}
        </x-primary-button>
    </div>
</form>
