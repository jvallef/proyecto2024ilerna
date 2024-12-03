# Análisis del Módulo Areas

Este documento proporciona un análisis detallado del módulo Areas del proyecto, siguiendo un orden lógico y cronológico de creación de los elementos.

## 1. Migración

### Migración Base (2024_10_13_181220_create_areas_table.php)
```php
Schema::create('areas', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->foreignId('user_id')->constrained('users');
    $table->foreignId('parent_id')->nullable()->constrained('areas');
    $table->boolean('featured')->default(false);
    $table->enum('status', ['draft', 'published', 'suspended'])->default('draft');
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('name');
    $table->index('slug');
    $table->index('user_id');
    $table->index('parent_id');
});
```

### Migración Adicional (2024_11_23_133205_add_sorting_and_meta_to_areas_table.php)
```php
Schema::table('areas', function (Blueprint $table) {
    $table->integer('sort_order')->default(0)->after('status');
    $table->json('meta')->nullable()->after('featured')
          ->comment('Para guardar metadatos adicionales, como SEO, configuraciones, etc.');
    
    $table->index('sort_order');
    $table->index(['parent_id', 'sort_order']);
    $table->index(['featured', 'sort_order']);
});
```

## 2. Modelo (Area.php)

### Traits y Relaciones
- Usa HasFactory, SoftDeletes, HasMediaTrait, GeneratesSlug
- Implementa HasMedia para gestión de medios

### Propiedades
```php
protected $fillable = [
    'name',
    'slug',
    'description',
    'user_id',
    'parent_id',
    'featured',
    'status',
    'sort_order',
    'meta'
];

protected $casts = [
    'featured' => 'boolean',
    'meta' => 'json'
];
```

### Relaciones
1. user(): BelongsTo - Relación con el usuario creador
2. parent(): BelongsTo - Relación con el área padre
3. children(): HasMany - Relación con áreas hijas
4. paths(): HasMany - Relación con rutas asociadas

### Métodos Principales
1. registerCoverMediaCollection(): Gestión de imágenes de portada
2. scopePublished(): Filtro para áreas publicadas
3. scopeFeatured(): Filtro para áreas destacadas
4. scopeOrdered(): Ordenamiento por sort_order
5. getFullPathAttribute(): Obtiene ruta jerárquica completa
6. getAreasByParentId(): Obtiene áreas hijas
7. getHierarchicalList(): Lista jerárquica para selects
8. getChildrenHierarchy(): Función auxiliar para jerarquía

## 3. Rutas

### Rutas Públicas
```php
Route::get('/areas', [AreaController::class, 'publicIndex'])->name('areas.index');
Route::get('/areas/{slug}', [AreaController::class, 'publicShow'])->name('areas.show');
```

### Rutas de Administración (middleware: auth, role:admin)
```php
// Gestión de papelera
Route::get('areas/trashed', [AreaController::class, 'privateTrashed'])->name('areas.trashed');
Route::patch('areas/{area}/restore', [AreaController::class, 'privateRestore'])->name('areas.restore');
Route::delete('areas/{area}/force-delete', [AreaController::class, 'privateForceDelete'])->name('areas.force-delete');

// CRUD principal
Route::get('areas/create', [AreaController::class, 'privateCreate'])->name('areas.create');
Route::get('areas/{area}/edit', [AreaController::class, 'privateEdit'])->name('areas.edit');
Route::get('areas', [AreaController::class, 'privateIndex'])->name('areas.index');
Route::post('areas', [AreaController::class, 'privateStore'])->name('areas.store');
Route::match(['put', 'patch'], 'areas/{area}', [AreaController::class, 'privateUpdate'])->name('areas.update');
Route::delete('areas/{area}', [AreaController::class, 'privateDestroy'])->name('areas.destroy');
Route::get('areas/{area}', [AreaController::class, 'privateShow'])->name('areas.show');
```

### Rutas de API para Búsqueda
```php
Route::get('/api/search/areas', [AreaSearchController::class, 'suggestions'])->name('api.areas.search');
Route::get('/api/search/areas/trashed', [AreaTrashedSearchController::class, 'suggestions'])->name('api.areas.trashed.search');
```

### Rutas Educativas
```php
// Para profesores (middleware: role:teacher)
Route::prefix('workarea')->name('workarea.')->group(function () {
    Route::get('areas', [AreaController::class, 'educaIndex'])->name('areas.index');
    Route::get('areas/{slug}', [AreaController::class, 'educaShow'])->name('areas.show');
    Route::get('areas/{slug}/progress', [AreaController::class, 'educaProgress'])->name('areas.progress');
});

// Para estudiantes (middleware: role:student)
Route::prefix('classroom')->name('classroom.')->group(function () {
    Route::get('areas', [AreaController::class, 'educaIndex'])->name('areas.index');
    Route::get('areas/{slug}', [AreaController::class, 'educaShow'])->name('areas.show');
    Route::get('areas/{slug}/progress', [AreaController::class, 'educaProgress'])->name('areas.progress');
});
```

## 4. Gestión de Imágenes y Búsqueda

### Integración con Spatie Media Library
El módulo utiliza el trait `HasMediaTrait` que proporciona:

1. Colecciones de Medios:
   - default: Colección general de medios
   - cover: Imágenes de portada (single file)
   - files: Archivos generales

2. Conversiones Automáticas:
   - thumb: Miniatura (dimensiones configurables)
   - medium: Tamaño medio
   - large: Tamaño grande

3. Métodos Principales:
   ```php
   // Registro de colecciones
   registerMediaCollections()
   
   // Registro de conversiones
   registerMediaConversions(Media $media = null)
   
   // Gestión de usuarios de medios
   getMediaUser()
   setMediaUser($userId)
   ```

4. Configuración:
   - Disco: public
   - Dimensiones: Configurables en config/media.php
   - Formatos soportados: jpg, jpeg, png, webp

### Sistema de Búsqueda
1. Controladores Específicos:
   - AreaSearchController: Búsqueda general
   - AreaTrashedSearchController: Búsqueda en papelera

2. Componente Frontend:
   - x-search-autocomplete: Búsqueda con sugerencias en tiempo real

## 5. Validaciones Especiales

### Prevención de Referencias Circulares
```php
protected function hasCircularReference($area, $newParentId, $visited = []): bool
{
    // Verifica si el área es su propio padre
    if ($area->id === $newParentId) {
        return true;
    }
    
    // Verifica descendientes
    $descendants = $area->children()->pluck('id')->toArray();
    return in_array($newParentId, $descendants);
}
```

### Validación de Nombres Únicos
```php
// En el método rules() de AreaRequest
if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
    $rules['name'][] = Rule::unique('areas')->ignore($this->route('area'));
} else {
    $rules['name'][] = 'unique:areas';
}
```

### Preparación de Datos Booleanos
```php
protected function prepareForValidation(): void
{
    if ($this->has('featured')) {
        $this->merge([
            'featured' => $this->featured === 'true' || 
                         $this->featured === '1' || 
                         $this->featured === true,
        ]);
    }
}
```

## 6. Middleware y Autorización

### Acceso a Sección Educativa
- Teachers: Acceso completo a funcionalidades educativas (workarea)
  - Visualización de áreas
  - Seguimiento de progreso
  - Gestión de contenido educativo
  
- Students: Acceso limitado (classroom)
  - Visualización de áreas publicadas
  - Seguimiento de progreso personal
  - Sin capacidad de modificación

### Métodos Educativos
1. educaIndex(): 
   - Accesible para teachers y students
   - Muestra áreas publicadas
   - Filtrado según rol del usuario

2. educaShow():
   - Accesible para teachers y students
   - Vista detallada del área
   - Contenido adaptado según rol

3. educaProgress():
   - Accesible para teachers y students
   - Teachers: Visualización de progreso de todos los estudiantes
   - Students: Solo su progreso personal

## 7. Vistas

### Sistema de Traducciones
El módulo utiliza el sistema de traducciones de Laravel con las siguientes características:

1. Archivo de Traducciones:
   - Ubicación: `resources/lang/es/messages.php`
   - Estructura: clave = texto exacto a mostrar
   ```php
   'Áreas' => 'Áreas',           // Español
   'Áreas' => 'Areas',           // Inglés (en messages.php de /en/)
   ```

2. Uso en Vistas:
   ```php
   {{ __('Áreas') }}                    // Título del módulo
   {{ __('Crear Área') }}               // Botón de crear
   {{ __('No se pueden crear referencias circulares en la jerarquía') }}  // Mensaje de error
   ```

3. Textos con Variables:
   ```php
   {{ __(':item creado correctamente', ['item' => 'Área']) }}
   ```

### Componentes
1. Generales:
   - x-input-label: `{{ __('Nombre') }}`
   - x-text-input: placeholder="{{ __('Introduce el nombre del área') }}"
   - x-input-error: mensajes de validación traducidos
   - x-required-mark
   - x-secondary-button: `{{ __('Cancelar') }}`
   - x-primary-button: `{{ __('Guardar') }}`

2. Específicos de Areas:
   - x-area-status: estados traducidos
   - x-area-actions: acciones traducidas
   - x-media.single-image-upload: textos de upload traducidos
   - x-search-autocomplete: placeholders traducidos

### Vistas Principales
1. index.blade.php:
   ```blade
   <h1>{{ __('Áreas') }}</h1>
   <h2>{{ __('Áreas destacadas') }}</h2>
   ```

2. form.blade.php (create/edit):
   ```blade
   <h1>{{ __('Crear Área') }}</h1>
   <h1>{{ __('Editar Área') }}</h1>
   ```

3. show.blade.php:
   ```blade
   <h2>{{ __('Detalles del Área') }}</h2>
   <p>{{ __('Creado el') }}: {{ $area->created_at }}</p>
   ```

4. trashed.blade.php:
   ```blade
   <h1>{{ __('Áreas eliminadas') }}</h1>
   ```

### Plan de Revisión de Traducciones

1. Archivos a Revisar:
   - Todas las vistas blade del módulo
   - Componentes relacionados
   - Mensajes de validación
   - Mensajes de respuesta JSON
   - Notificaciones

2. Proceso de Revisión:
   ```bash
   # Buscar textos hardcodeados en vistas
   grep -r ">" resources/views/areas
   
   # Buscar textos en componentes
   grep -r ">" resources/views/components
   
   # Revisar mensajes de validación
   grep -r "message" app/Http/Requests
   ```

3. Puntos de Atención:
   - Textos directos en HTML
   - Placeholders de inputs
   - Mensajes de error
   - Títulos y encabezados
   - Botones y enlaces
   - Tooltips y ayudas
   - Mensajes de confirmación

## 8. Gestión de Idiomas

### Estructura y Ubicación
```
resources/lang/
├── es/
│   ├── areas.php      # Textos específicos del módulo
│   ├── validation.php # Mensajes de validación
│   └── messages.php   # Mensajes generales
└── en/
    ├── areas.php
    ├── validation.php
    └── messages.php
```

### Organización de Traducciones
Las traducciones se centralizan en el archivo `resources/lang/es/messages.php`, utilizando una estructura plana donde la clave es el texto exacto a traducir:

```php
// resources/lang/es/messages.php
return [
    'Áreas' => 'Áreas',           // Español
    'Áreas' => 'Areas',           // Inglés (en messages.php de /en/)
];
```

### Uso en el Código
1. En Vistas:
   ```blade
   {{ __('Áreas') }}
   {{ __('Ver detalles') }}
   ```

2. En Controladores:
   ```php
   return back()->with('error', __('No se puede eliminar un área que tiene sub-áreas'));
   ```

3. En Validaciones:
   ```php
   'name.required' => __('El campo :attribute es obligatorio')
   ```

### Textos Pendientes de Traducción
1. Mensajes de Error en AreaService:
   ```php
   throw new \Exception('No se puede eliminar un área que tiene sub-áreas.');
   ```

2. Etiquetas en form.blade.php:
   ```blade
   <option value="">Selecciona un área padre</option>
   ```

3. Estados en index.blade.php:
   ```blade
   <span>Borrador</span>
   <span>Publicado</span>
   <span>Suspendido</span>
   ```

### Plan de Implementación
1. Crear archivos de idioma base (de momento tendrmos solo 'es', pero quedaría preparado para traducir fácilmente a inglés)
2. Mover todos los textos hardcodeados a los archivos de traducción
3. Reemplazar textos en código por llamadas a helper de traducción
4. Implementar fallback a español si falta traducción
5. Añadir nuevos idiomas según necesidad

### Variables de Entorno Relacionadas
```env
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
```

## 9. Variables de Entorno
- APP_LOCALE: Idioma principal (default: es)
- APP_FALLBACK_LOCALE: Idioma de respaldo (default: es)
- PAGINATION_PER_PAGE: Elementos por página (default: 12)
- MEDIA_MAX_FILE_SIZE: Tamaño máximo general (default: 10240)
- MEDIA_MAX_DIMENSIONS: Dimensiones máximas (default: 2048)
- COVER_MAX_FILE_SIZE: Tamaño máximo de covers (default: 2048)
- COVER_MAX_DIMENSIONS: Dimensiones máximas de covers (default: 2000)
- MEDIA_THUMB_SIZE: Tamaño de miniaturas (default: 150)
- MEDIA_MEDIUM_SIZE: Tamaño medio (default: 800)
- MEDIA_LARGE_SIZE: Tamaño grande (default: 1600)

## 10. Configuración de Media y Archivos Afectados

### Configuración
```php
// config/media.php
'cover' => [
    'max_file_size' => env('COVER_MAX_FILE_SIZE', 2048),
    'max_dimensions' => env('COVER_MAX_DIMENSIONS', 2000),
    'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
],
'conversions' => [
    'thumb' => [
        'width' => env('MEDIA_THUMB_SIZE', 150),
        'height' => env('MEDIA_THUMB_SIZE', 150),
    ],
    'medium' => [
        'width' => env('MEDIA_MEDIUM_SIZE', 800),
        'height' => env('MEDIA_MEDIUM_SIZE', 800),
    ],
    'large' => [
        'width' => env('MEDIA_LARGE_SIZE', 1600),
        'height' => env('MEDIA_LARGE_SIZE', 1600),
    ],
]
```

### Archivos que Usan esta Configuración

1. `app/Traits/HasMediaTrait.php`:
   ```php
   public function registerMediaCollections(): void
   {
       // ...
       if (method_exists($this, 'registerCoverMediaCollection')) {
           $this->addMediaCollection('cover')
               ->singleFile()
               ->useDisk('public')
               ->acceptsFile(function ($file) {
                   return in_array(
                       $file->mimeType, 
                       array_map(fn($ext) => 'image/' . $ext, config('media.cover.allowed_types'))
                   );
               })
               ->withResponsiveImages();
       }
   }
   ```

2. `app/Http/Requests/AreaRequest.php`:
   ```php
   'cover' => [
       'nullable',
       'file',
       'mimes:' . implode(',', config('media.cover.allowed_types')),
       'max:' . config('media.cover.max_file_size'),
       'dimensions:max_width=' . config('media.cover.max_dimensions') . 
           ',max_height=' . config('media.cover.max_dimensions')
   ],
   ```

3. `resources/views/components/media/single-image-upload.blade.php`:
   ```blade
   <x-media-upload
       :maxSize="config('media.cover.max_file_size')"
       :maxDimensions="config('media.cover.max_dimensions')"
       :allowedTypes="config('media.cover.allowed_types')"
   />
   ```

## 11. Request (AreaRequest.php)

### Reglas de Validación
```php
'name' => ['required', 'string', 'min:3', 'max:255'],
'description' => ['nullable', 'string', 'max:1000'],
'parent_id' => ['nullable', 'exists:areas,id'],
'featured' => ['boolean'],
'status' => ['required', Rule::in(['draft', 'published', 'suspended'])],
'sort_order' => ['nullable', 'integer', 'min:0'],
'cover' => [
    'nullable',
    'file',
    'mimes:jpg,jpeg,png,webp',
    'max:' . env('COVER_MAX_FILE_SIZE', 2048),
    'dimensions:max_width=' . env('COVER_MAX_DIMENSIONS', 2000) . 
        ',max_height=' . env('COVER_MAX_DIMENSIONS', 2000)
],
'meta' => ['nullable', 'array'],
'meta.title' => ['nullable', 'string', 'max:60'],
'meta.description' => ['nullable', 'string', 'max:160'],
'meta.keywords' => ['nullable', 'string', 'max:255']
```

### Validaciones Especiales
- Prevención de referencias circulares en jerarquía
- Validación condicional para nombres únicos en actualizaciones
- Preparación de datos booleanos

## 12. Servicio (AreaService.php)

### Métodos Principales
1. create(array $data): Area
   - Crea nueva área
   - Gestiona sort_order automático
   - Procesa imagen de portada
   - Usa transacciones DB

2. update(Area $area, array $data): Area
   - Actualiza área existente
   - Gestiona cambios de jerarquía
   - Actualiza sort_order si necesario
   - Procesa imagen de portada

3. delete(Area $area): bool
   - Verifica dependencias (áreas hijas)
   - Reordena áreas hermanas
   - Elimina medios asociados

4. updateOrder(array $orderedIds, ?int $parentId): void
   - Actualiza orden de áreas
   - Gestiona jerarquía

## 13. Controlador (AreaController.php)

### Middleware y Autorización
- Admin: gestión completa de áreas
- Auth: acceso a sección educativa (Creo que... ¿teachers y students?)

### Métodos Públicos
1. publicIndex(): Lista áreas publicadas
2. publicShow($slug): Muestra área pública

### Métodos Privados (Admin)
1. privateIndex(): Lista todas las áreas
2. privateCreate(): Formulario de creación
3. privateStore(): Almacena nueva área
4. privateShow(): Muestra área en admin
5. privateEdit(): Formulario de edición
6. privateUpdate(): Actualiza área
7. privateDestroy(): Elimina área
8. privateTrashed(): Lista áreas eliminadas
9. privateRestore(): Restaura área
10. privateForceDelete(): Elimina permanentemente

### Métodos Educativos
1. educaIndex(): Lista áreas ¿para teachers y students?
2. educaShow(): Muestra área educativa
3. educaProgress(): Muestra progreso

## 14. Vistas

### Layouts
- Usa x-app-layout (layout principal de la aplicación)
- Compartido con otros módulos como Users

### Componentes
1. Generales:
   - x-input-label
   - x-text-input
   - x-input-error
   - x-required-mark
   - x-secondary-button
   - x-primary-button

2. Específicos de Areas:
   - x-area-status
   - x-area-actions
   - x-media.single-image-upload
   - x-search-autocomplete

### Vistas Principales
1. index.blade.php:
   - Lista de áreas destacadas
   - Tabla de áreas regulares
   - Buscador con autocompletado
   - Paginación
   - Acciones CRUD

2. form.blade.php (create/edit):
   - Información básica
   - Selección de área padre
   - Estado y destacado
   - Gestión de imagen
   - Metadatos SEO
   - Validación client-side

3. show.blade.php:
   - Detalles del área
   - Imagen de portada
   - Metadatos
   - Áreas relacionadas

4. trashed.blade.php:
   - Lista de áreas eliminadas
   - Opciones de restauración
   - Eliminación permanente

5. progress.blade.php:
   - Estadísticas de progreso
   - Gráficos de avance
   - Filtros por periodo

## 15. Testing y Jerarquía

### Datos de Prueba
- En UserSeeder.php se crean usuarios del sistema.
- En el resto de seeders se crean areas, paths y cursos.
- Puedes usar los registros existentes o bien crear los tuyos propios. Pero solo puedes modificar o eliminar los que creaste.

### Estructura Jerárquica del Sistema

```
Areas
└── Paths
    └── Courses
        └── Contents
```

#### Areas (Nivel Superior)
- Representan las categorías principales del sistema educativo
- Pueden tener sub-áreas (estructura jerárquica propia)
- Contienen múltiples Paths

#### Paths (Dependientes de Areas)
- Rutas de aprendizaje específicas dentro de un área
- Siempre pertenecen a un área específica
- Contienen una secuencia ordenada de cursos

### Matriz de Permisos por Rol

#### Admin
- Permisos Completos:
  - manage users
  - manage areas
  - view areas
  - restore areas
  - manage paths
  - view paths
  - manage courses
  - view courses
  - manage contents
  - view contents
  - manage comments
  - view comments
  - manage medias
  - view medias

#### Teacher
- Permisos de Visualización:
  - view areas
  - view paths
  - view courses
  - edit own courses
  - view contents
  - edit own contents
  - view comments
  - edit own comments
  - view medias
  - edit own medias

#### Student
- Permisos de Lectura:
  - view areas
  - view paths
  - view courses
  - view contents
  - view comments
  - edit own comments
  - view medias

### Estados y Transiciones

#### Estados de Área
1. `draft`: Borrador inicial
2. `published`: Área publicada y visible
3. `suspended`: Área temporalmente suspendida

### Casos de Prueba Básicos

#### 1. Pruebas de Creación
- Crear área con datos válidos
- Crear sub-área (validar jerarquía)
- Validar campos requeridos

#### 2. Pruebas de Jerarquía
- Verificar relación padre-hijo
- Prevenir referencias circulares
- Validar orden de áreas hermanas

#### 3. Pruebas de Estado
- Cambiar estado a published
- Cambiar estado a suspended
- Validar visibilidad según estado

#### 4. Pruebas de Permisos
- Acceso admin a todas las operaciones
- Acceso de lectura para teacher
- Acceso de lectura para student

#### 5. Pruebas de Eliminación
- Soft delete de área sin dependencias
- Prevenir eliminación con sub-áreas
- Restaurar área eliminada

### Casos de Error Principales

#### Errores de Jerarquía
- No se pueden crear referencias circulares
- No se puede eliminar un área con sub-áreas
- No se puede mover un área a un descendiente propio

#### Errores de Permisos
- No autorizado para crear áreas
- No autorizado para editar áreas
- No autorizado para eliminar áreas
- No autorizado para restaurar áreas

#### Errores de Validación
- Nombre requerido (mínimo 3 caracteres)
- Slug único
- Estado válido (draft/published/suspended)

### Eventos Básicos

#### Eventos de Área
1. `AreaCreated`: Al crear una nueva área
2. `AreaUpdated`: Al actualizar un área existente
3. `AreaDeleted`: Al eliminar un área (soft delete)
4. `AreaStatusChanged`: Al cambiar el estado

### Soft Deletes

#### Comportamiento Básico
1. El área se marca como eliminada
2. No aparece en consultas regulares
3. Mantiene sus relaciones para referencia
4. Se puede restaurar si es necesario
