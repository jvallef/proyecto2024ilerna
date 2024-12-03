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

## 3. Request (AreaRequest.php)

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
    'dimensions:max_width=' . env('COVER_MAX_DIMENSIONS', 2000) . ',max_height=' . env('COVER_MAX_DIMENSIONS', 2000)
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

## 4. Servicio (AreaService.php)

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

## 5. Controlador (AreaController.php)

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

## 6. Vistas

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

## 7. Variables de Entorno
- PAGINATION_PER_PAGE: 12 (default)
- COVER_MAX_FILE_SIZE: 2048 (default)
- COVER_MAX_DIMENSIONS: 2000 (default)

## 8. Gestión de Idiomas
### Textos Traducibles
1. Mensajes de validación
2. Etiquetas de formularios
3. Mensajes de éxito/error
4. Estados de áreas
5. Textos de interfaz

### Archivos de Idiomas
- Necesita implementación de archivos de traducción
- Uso consistente de __() para traducciones
- Falta definir traducciones para algunos textos hardcodeados

### Consideraciones
1. Algunos textos están hardcodeados en español
2. Falta implementar traducciones para metadatos SEO
3. Necesidad de revisar y estandarizar el uso de traducciones en todo el módulo
