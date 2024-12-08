<form method="POST" 
    action="{{ isset($course) && $course->id ? route('admin.courses.update', $course) : route('admin.courses.store') }}" 
    enctype="multipart/form-data" 
    class="space-y-6">
    @csrf
    @if(isset($course) && $course->id)
        @method('PUT')
    @endif

    <!-- Información Básica -->
    <div class="border-b border-gray-200 pb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Información básica') }}</h3>
        
        <!-- Primera fila: Título y Grupo de edad -->
        <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Título -->
            <div>
                <div class="flex items-center">
                    <x-input-label for="title" :value="__('Título')" />
                    <x-required-mark />
                </div>
                <x-text-input id="title" 
                         name="title" 
                         type="text" 
                         class="mt-1 block w-full" 
                         :value="old('title', $course->title ?? '')" 
                         required 
                         autofocus />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <!-- Grupo de edad -->
            <div>
                <div class="flex items-center">
                    <x-input-label for="age_group" :value="__('Grupo de Edad')" />
                </div>
                <select id="age_group" 
                        name="age_group"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    @foreach(App\Enums\AgeGroup::labels() as $value => $label)
                        <option value="{{ $value }}" {{ old('age_group', $course->age_group ?? '') === $value ? 'selected' : '' }}>
                            {{ __($label) }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('age_group')" class="mt-2" />
            </div>
        </div>

        <!-- Segunda fila: Estado -->
        <div class="mt-4">
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
                    <option value="draft" {{ old('status', $course->status ?? 'draft') === 'draft' ? 'selected' : '' }}>
                        {{ __('Borrador') }}
                    </option>
                    <option value="published" {{ old('status', $course->status ?? '') === 'published' ? 'selected' : '' }}>
                        {{ __('Publicado') }}
                    </option>
                    <option value="suspended" {{ old('status', $course->status ?? '') === 'suspended' ? 'selected' : '' }}>
                        {{ __('Suspendido') }}
                    </option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>
        </div>

        <!-- Tercera fila: Descripción -->
        <div class="mt-4">
            <div>
                <div class="flex items-center">
                    <x-input-label for="description" :value="__('Descripción')" />
                    <x-required-mark />
                </div>
                <textarea id="description" 
                          name="description" 
                          rows="3"
                          class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                          required>{{ old('description', $course->description ?? '') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
        </div>
    </div>

    <!-- Rutas Asociadas -->
    <div class="border-b border-gray-200 pb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Rutas Asociadas') }}</h3>
        <div class="mt-4">
            <div class="flex items-center">
                <x-input-label for="paths" :value="__('Selecciona las rutas')" />
                <x-required-mark />
            </div>
            <select id="paths" 
                    name="paths[]"
                    multiple
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    required>
                @foreach($pathsList as $pathId => $pathName)
                    <option value="{{ $pathId }}" 
                        {{ (isset($course) && $course->paths->contains($pathId)) || (is_array(old('paths')) && in_array($pathId, old('paths', []))) ? 'selected' : '' }}>
                        {!! $pathName !!}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('paths')" class="mt-2" />
        </div>
    </div>

    <!-- Imagen y Opciones -->
    <div class="border-b border-gray-200 pb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Imagen y Opciones') }}</h3>
        
        <!-- Imagen de portada -->
        <div>
            <x-media.single-image-upload
                :model="isset($course) ? $course : null"
                name="cover"
                :label="__('Imagen de Portada')"
                collection="cover"
                :required="false"
            />
        </div>

        <!-- Destacado -->
        <div class="mt-4">
            <div class="relative flex items-start">
                <div class="flex items-center h-5">
                    <input type="checkbox" 
                           id="featured" 
                           name="featured" 
                           value="1" 
                           {{ old('featured', $course->featured ?? false) ? 'checked' : '' }}
                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="featured" class="font-medium text-gray-700">{{ __('Destacado') }}</label>
                    <p class="text-gray-500">{{ __('Marca esta opción para destacar el curso en la página principal') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Metadatos -->
    <div class="border-b border-gray-200 pb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Metadatos SEO') }}</h3>
        
        <!-- Primera fila: Título SEO y Keywords -->
        <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- SEO Título -->
            <div>
                <x-input-label for="meta_title" :value="__('Título SEO')" />
                <x-text-input id="meta_title" 
                         name="meta[title]" 
                         type="text" 
                         class="mt-1 block w-full" 
                         :value="old('meta.title', $course->meta['title'] ?? '')" 
                         maxlength="60" />
                <p class="mt-1 text-sm text-gray-500">{{ __('Máximo 60 caracteres') }}</p>
                <x-input-error :messages="$errors->get('meta.title')" class="mt-2" />
            </div>

            <!-- SEO Keywords -->
            <div>
                <x-input-label for="meta_keywords" :value="__('Palabras clave')" />
                <x-text-input id="meta_keywords" 
                         name="meta[keywords]" 
                         type="text" 
                         class="mt-1 block w-full" 
                         :value="old('meta.keywords', $course->meta['keywords'] ?? '')" />
                <p class="mt-1 text-sm text-gray-500">{{ __('Separadas por comas') }}</p>
                <x-input-error :messages="$errors->get('meta.keywords')" class="mt-2" />
            </div>
        </div>

        <!-- Segunda fila: Descripción SEO -->
        <div class="mt-4">
            <div class="sm:col-span-1 max-w-md">
                <x-input-label for="meta_description" :value="__('Descripción SEO')" />
                <textarea id="meta_description" 
                          name="meta[description]" 
                          rows="3"
                          maxlength="160" 
                          class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('meta.description', $course->meta['description'] ?? '') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">{{ __('Máximo 160 caracteres') }}</p>
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
            {{ isset($course) && $course->id ? __('Actualizar') : __('Crear') }}
        </x-primary-button>
    </div>
</form>