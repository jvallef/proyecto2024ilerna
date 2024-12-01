<!-- Formulario -->
<form action="{{ $area->exists ? route('admin.areas.update', $area) : route('admin.areas.store') }}" 
      method="POST" 
      enctype="multipart/form-data"
      class="space-y-6">
    @csrf
    @if($area->exists)
        @method('PUT')
    @endif

    <!-- Nombre -->
    <div>
        <x-input-label for="name" :value="__('Nombre')" />
        <x-text-input id="name" 
                     name="name" 
                     type="text" 
                     class="mt-1 block w-full" 
                     :value="old('name', $area->name)" 
                     required 
                     autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <!-- Descripción -->
    <div>
        <x-input-label for="description" :value="__('Descripción')" />
        <textarea id="description" 
                  name="description" 
                  rows="3" 
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $area->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <!-- Área Padre -->
    <div>
        <x-input-label for="parent_id" :value="__('Área Padre')" />
        <x-search-autocomplete
            :route="route('admin.areas.index')"
            :search-url="route('admin.api.areas.search')"
            :placeholder="__('Buscar área padre...')"
            :min-chars="2"
            :initial-value="$area->parent ? $area->parent->name . ' || ' . Str::limit($area->parent->description, 50) : ''"
            :initial-id="$area->parent_id"
            input-name="parent_id"
        />
        <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
    </div>

    <!-- Estado -->
    <div>
        <x-input-label for="status" :value="__('Estado')" />
        <select id="status" 
                name="status" 
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
            <option value="draft" {{ old('status', $area->status) === 'draft' ? 'selected' : '' }}>Borrador</option>
            <option value="published" {{ old('status', $area->status) === 'published' ? 'selected' : '' }}>Publicado</option>
            <option value="suspended" {{ old('status', $area->status) === 'suspended' ? 'selected' : '' }}>Suspendido</option>
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <!-- Destacado -->
    <div class="flex items-center">
        <input type="checkbox" 
               id="featured" 
               name="featured" 
               value="1" 
               {{ old('featured', $area->featured) ? 'checked' : '' }}
               class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600">
        <x-input-label for="featured" :value="__('Destacar esta área')" class="ml-2" />
        <x-input-error :messages="$errors->get('featured')" class="mt-2" />
    </div>

    <!-- Imagen -->
    <div>
        <x-input-label for="image" :value="__('Imagen')" />
        <x-media.single-image-upload
            :model="$area"
            collection="areas"
            :max-size="2048"
            :max-dimensions="3000"
            accept="image/jpeg,image/png,image/webp"
        />
        <x-input-error :messages="$errors->get('image')" class="mt-2" />
    </div>

    <!-- SEO Metadatos -->
    <div class="space-y-4 border-t pt-4 mt-4">
        <h3 class="text-lg font-medium">Metadatos SEO</h3>
        
        <!-- SEO Título -->
        <div>
            <x-input-label for="meta[seo_title]" :value="__('Título SEO')" />
            <x-text-input id="meta[seo_title]" 
                         name="meta[seo_title]" 
                         type="text" 
                         class="mt-1 block w-full" 
                         :value="old('meta.seo_title', $area->meta['seo_title'] ?? '')" 
                         maxlength="60" />
            <p class="mt-1 text-sm text-gray-500">Máximo 60 caracteres</p>
            <x-input-error :messages="$errors->get('meta.seo_title')" class="mt-2" />
        </div>

        <!-- SEO Descripción -->
        <div>
            <x-input-label for="meta[seo_description]" :value="__('Descripción SEO')" />
            <textarea id="meta[seo_description]" 
                      name="meta[seo_description]" 
                      rows="2"
                      maxlength="160" 
                      class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('meta.seo_description', $area->meta['seo_description'] ?? '') }}</textarea>
            <p class="mt-1 text-sm text-gray-500">Máximo 160 caracteres</p>
            <x-input-error :messages="$errors->get('meta.seo_description')" class="mt-2" />
        </div>

        <!-- SEO Keywords -->
        <div>
            <x-input-label for="meta[seo_keywords]" :value="__('Palabras clave SEO')" />
            <x-text-input id="meta[seo_keywords]" 
                         name="meta[seo_keywords]" 
                         type="text" 
                         class="mt-1 block w-full" 
                         :value="old('meta.seo_keywords', $area->meta['seo_keywords'] ?? '')" />
            <p class="mt-1 text-sm text-gray-500">Separadas por comas</p>
            <x-input-error :messages="$errors->get('meta.seo_keywords')" class="mt-2" />
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="flex justify-end space-x-4">
        <x-secondary-button type="button" onclick="window.history.back()">
            {{ __('Cancelar') }}
        </x-secondary-button>

        <x-primary-button>
            {{ $area->exists ? __('Actualizar') : __('Crear') }}
        </x-primary-button>
    </div>
</form>
