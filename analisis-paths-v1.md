# Análisis del Módulo Paths - Lista de Pasos

El objetivo es desarrollar el módulo de Paths para la plataforma EduPlazza, siguiendo el planteamiento que hemos hecho con Areas, según analisis-areas-v2.md.

- El sistema tiene que ser lo más simple posible.
- Hay que seguir la filosofía de lo que se ha hecho con Areas.
- Si fuera preciso, seguir la filosofía de lo que se ha hecho con Users.

## Formato de Prompt

### Pasos Previos Genéricos
Antes de ejecutar cualquier tarea:

1. Verificar el estado actual
   - Buscar los propios archivos/componentes que se indican en el prompt u otros existentes relacionados con la tarea
   - Revisar la estructura y contenido actual
   - Identificar dependencias y relaciones

2. Analizar el impacto
   - Evaluar cómo afecta a otros componentes
   - Identificar posibles conflictos
   - Verificar la compatibilidad con el sistema actual

3. Validar requisitos
   - Confirmar que tenemos toda la información necesaria
   - Verificar que los requisitos son consistentes
   - Identificar posibles ambigüedades

4. Planificar la ejecución
   - Determinar el orden de los cambios
   - Identificar puntos de verificación
   - Preparar plan de rollback si es necesario

Para mantener la consistencia y evitar errores, cada tarea debe seguir este formato:

### Contexto Específico

CONTEXTO INMEDIATO:
- Archivo/s a modificar: [lista]
- Funcionalidad actual: [descripción]
- Campos/métodos/relaciones: [lista]
- Dependencias: [traits, interfaces, observers]
- Validaciones existentes: [reglas]
- Eventos actuales: [lista]

### Restricciones Explícitas

RESTRICCIONES:
- NO crear nuevas funcionalidades
- NO modificar: [lista]
- MANTENER: [lista]
- USAR SOLO: [lista]
- RESPETAR estructura de: [elementos]
- SEGUIR patrones de: [elementos]

### Referencia Concreta

REFERENCIA:
- Implementación base: [archivo/funcionalidad]
- Documentación: [archivos]
- Tests relacionados: [archivos]
- Commits relevantes: [lista]

### Objetivo Simple

TAREA:
- Acción concreta: [descripción]
- Resultado esperado: [descripción]
- Dependencias previas: [lista]

### Verificación

VERIFICACIÓN:
- Comprobar: [lista]
- Validar: [lista]
- Tests necesarios: [lista]
- Integridad referencial: [checks]

### Ejemplo Real

```code
PASOS PREVIOS GENÉRICOS
Antes de ejecutar cualquier tarea:

1. Verificar el estado actual
   - Buscar los propios archivos/componentes que se indican en el prompt u otros existentes relacionados con la tarea
   - Revisar la estructura y contenido actual
   - Identificar dependencias y relaciones

2. Analizar el impacto
   - Evaluar cómo afecta a otros componentes
   - Identificar posibles conflictos
   - Verificar la compatibilidad con el sistema actual

3. Validar requisitos
   - Confirmar que tenemos toda la información necesaria
   - Verificar que los requisitos son consistentes
   - Identificar posibles ambigüedades

4. Planificar la ejecución
   - Determinar el orden de los cambios
   - Identificar puntos de verificación
   - Preparar plan de rollback si es necesario

CONTEXTO INMEDIATO:
- Archivo: app/Models/Path.php
- Funcionalidad: Relación con Area
- Campos: id, name, slug, area_id, parent_id, status
- Dependencias: HasFactory, SoftDeletes, HasMediaTrait
- Validaciones: unique:paths,name, exists:areas,id
- Eventos: PathObserver (creating, updating)

RESTRICCIONES:
- NO crear nuevos campos
- NO modificar estructura de tabla
- MANTENER patrón de Areas
- USAR SOLO traits existentes
- RESPETAR jerarquía de paths
- SEGUIR validaciones de Area

REFERENCIA:
- Implementación base: app/Models/Area.php
- Documentación: analisis-paths-v1.md
- Tests: tests/Unit/AreaTest.php
- Commits: feat/areas-module

TAREA:
- Acción: Añadir relación belongsTo con Area
- Resultado: Path debe pertenecer a un Area activo
- Dependencias: Area model, migrations

VERIFICACIÓN:
- Comprobar relación bidireccional Area-Path
- Validar restricción de área activa
- Tests: creación, actualización, eliminación
- Integridad: no paths huérfanos, área existente
```

## Pasos a Seguir

1. **Migración de Base de Datos**
   - Revisar migraciones de tabla paths
   - Crear migración para metadatos y ordenamiento si no existen
   - Revisar estado de los campos, índices y relaciones

   ## 1. Análisis de Migraciones

   ### Estado Actual

   1. **Migración Base (2024_10_13_181225_create_paths_table.php)**
      - ✅ Campos básicos implementados:
        - id, name, slug, description
        - user_id (creador)
        - parent_id (path padre)
        - area_id (área a la que pertenece)
        - featured, status
        - timestamps y softDeletes
      - ✅ Índices implementados para:
        - name, slug
        - user_id, area_id, parent_id

   2. **Campos Faltantes (comparando con Areas)**
      - ❌ sort_order: Para ordenamiento manual
      - ❌ meta: Para metadatos adicionales (SEO, configs)
      - ❌ Índices para sort_order

   ### Acciones Necesarias

   1. **Crear Migración Principal**
   ```php
   Schema::create('paths', function (Blueprint $table) {
       $table->id();
       $table->string('name');
       $table->string('slug')->unique();
       $table->text('description')->nullable();
       $table->foreignId('user_id')->constrained();
       $table->foreignId('parent_id')->nullable()->constrained('paths')->onDelete('restrict');
       $table->foreignId('area_id')->constrained()->onDelete('restrict'); 
       $table->boolean('featured')->default(false);
       $table->string('status')->default('draft');
       $table->integer('sort_order')->default(0);
       $table->json('meta')->nullable();
       $table->softDeletes();
       $table->timestamps();

       $table->index('area_id'); 
   });
   ```

   ### Notas
   - La estructura base es similar a Areas
   - Se mantiene la jerarquía (parent_id)
   - Se añade relación con area_id
   - Se necesitan los mismos campos de ordenamiento y meta que Areas para mantener consistencia

2. **Modelo Path**
   - Revisar modelo Path existente
   - Implementar traits si fueran necesarios
   - Definir relaciones (Area, Courses, User)
   - Configurar propiedades y atributos
   - Implementar métodos de utilidad

   ## 2. Análisis del Modelo Path

   ### Estado Actual

   1. **Modelo Base (app/Models/Path.php)**
      - ✅ Traits implementados:
        - HasFactory
        - SoftDeletes
        - InteractsWithMedia (diferente a Areas que usa HasMediaTrait, debe usar el mismo que Areas)
        - GeneratesSlug
      - ✅ Relaciones implementadas:
        - user(): BelongsTo
        - parent(): BelongsTo
        - children(): HasMany
        - area(): BelongsTo
        - courses(): BelongsToMany
      - ✅ Propiedades básicas:
        - fillable: campos básicos definidos
        - casts: featured como boolean
        - dates: deleted_at

   2. **Elementos Faltantes (comparando con Areas)**
      - ❌ Trait HasMediaTrait (usa InteractsWithMedia en su lugar y no debe ser así)
      - ❌ Método registerCoverMediaCollection()
      - ❌ Método booted() para limpiar medios al eliminar
      - ❌ sort_order y meta en $fillable
      - ❌ meta en $casts
      - ❌ Método childrenAlphabetically()
      - ❌ Scopes útiles (published, featured, ordered)

   ### Acciones Necesarias

   1. **Actualizar Traits**
      ```php
      // Cambiar InteractsWithMedia por HasMediaTrait para consistencia
      use HasFactory, SoftDeletes, HasMediaTrait, GeneratesSlug;
      ```

   2. **Añadir Métodos de Media**
      ```php
      public function registerCoverMediaCollection(): void
      {
          // Método vacío para activar la colección 'cover' en HasMediaTrait
      }

      protected static function booted()
      {
          parent::booted();
          static::deleting(function ($path) {
              $path->clearMediaCollection('cover');
          });
      }
      ```

   3. **Actualizar Propiedades**
      ```php
      protected $fillable = [
          'name',
          'slug',
          'description',
          'user_id',
          'parent_id',
          'area_id',
          'featured',
          'status',
          'sort_order',  // Añadir
          'meta'         // Añadir
      ];

      protected $casts = [
          'featured' => 'boolean',
          'meta' => 'json'    // Añadir
      ];
      ```

   4. **Añadir Métodos Útiles**
      ```php
      public function childrenAlphabetically(): HasMany
      {
          return $this->hasMany(Path::class, 'parent_id')
              ->orderBy(DB::raw('LOWER(name)'));
      }

      // Scopes
      public function scopePublished($query) { ... }
      public function scopeFeatured($query) { ... }
      public function scopeOrdered($query) { ... }
      ```

   ### Notas
   - Se mantiene la misma estructura que Areas
   - Se añade relación con area_id
   - Se necesita unificar la gestión de medios usando HasMediaTrait
   - Importante mantener la consistencia en el manejo de ordenamiento y metadatos

3. **Sistema de Rutas**
   - Revisar rutas existentes para Paths
   - Configurar rutas públicas
   - Configurar rutas administrativas
   - Configurar rutas de API, ojo como están hechas las de Areas, porque esto nos ha dado problemas
   - Configurar rutas educativas (workarea/classroom)

   ## 3. Análisis del Sistema de Rutas

   ### Estado Actual

   1. **Rutas de Areas (implementadas)**
      - ✅ Rutas públicas:
        - GET /areas (index)
        - GET /areas/{slug} (show)
      - ✅ Rutas administrativas:
        - CRUD completo
        - Gestión de papelera (trashed, restore, force-delete)
      - ✅ Rutas de API:
        - GET /api/search/areas
        - GET /api/search/areas/trashed
      - ✅ Rutas educativas:
        - Workarea (profesores)
        - Classroom (estudiantes)
        - Progreso incluido

   2. **Rutas de Paths**
      - ❌ No se encuentran rutas implementadas
      - ❌ Falta estructura completa de rutas

   ### Acciones Necesarias

   1. **Implementar Rutas Públicas**
      ```php
      // Rutas públicas
      Route::get('/paths', [PathController::class, 'publicIndex'])->name('paths.index');
      Route::get('/paths/{slug}', [PathController::class, 'publicShow'])->name('paths.show');
      ```

   2. **Implementar Rutas Administrativas**
      ```php
      Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
          // Gestión de papelera
          Route::get('paths/trashed', [PathController::class, 'privateTrashed'])->name('paths.trashed');
          Route::patch('paths/{path}/restore', [PathController::class, 'privateRestore'])->name('paths.restore');
          Route::delete('paths/{path}/force-delete', [PathController::class, 'privateForceDelete'])->name('paths.force-delete');

          // CRUD principal
          Route::get('paths/create', [PathController::class, 'privateCreate'])->name('paths.create');
          Route::get('paths/{path}/edit', [PathController::class, 'privateEdit'])->name('paths.edit');
          Route::get('paths', [PathController::class, 'privateIndex'])->name('paths.index');
          Route::post('paths', [PathController::class, 'privateStore'])->name('paths.store');
          Route::match(['put', 'patch'], 'paths/{path}', [PathController::class, 'privateUpdate'])->name('paths.update');
          Route::delete('paths/{path}', [PathController::class, 'privateDestroy'])->name('paths.destroy');
          Route::get('paths/{path}', [PathController::class, 'privateShow'])->name('paths.show');

          // Rutas de búsqueda
          Route::get('/api/search/paths', [PathSearchController::class, 'suggestions'])->name('api.paths.search');
          Route::get('/api/search/paths/trashed', [PathTrashedSearchController::class, 'suggestions'])->name('api.paths.trashed.search');
      });
      ```

   3. **Implementar Rutas Educativas**
      ```php
      // Para profesores
      Route::middleware(['role:teacher'])->prefix('workarea')->name('workarea.')->group(function () {
          Route::get('paths', [PathController::class, 'educaIndex'])->name('paths.index');
          Route::get('paths/{slug}', [PathController::class, 'educaShow'])->name('paths.show');
          Route::get('paths/{slug}/progress', [PathController::class, 'educaProgress'])->name('paths.progress');
      });

      // Para estudiantes
      Route::middleware(['role:student'])->prefix('classroom')->name('classroom.')->group(function () {
          Route::get('paths', [PathController::class, 'educaIndex'])->name('paths.index');
          Route::get('paths/{slug}', [PathController::class, 'educaShow'])->name('paths.show');
          Route::get('paths/{slug}/progress', [PathController::class, 'educaProgress'])->name('paths.progress');
      });
      ```

   ### Notas
   - Se mantiene la misma estructura que Areas
   - Se necesita implementar todas las rutas desde cero
   - Importante mantener la consistencia en nombres y estructura
   - Las rutas de API deben seguir el mismo patrón que Areas
   - Se mantienen los mismos middlewares y prefijos

4. **Controladores**
   - Revisar controladores existentes
   - Desarrollar PathController
   - Implementar PathSearchController
   - Implementar PathTrashedSearchController

   ## 4. Análisis de Controladores

   ### Estado Actual

   1. **Controladores de Areas (implementados)**
      - ✅ AreaController:
        - Usa AreaService para lógica de negocio
        - Middlewares configurados (auth, roles)
        - Métodos públicos (index, show)
        - Métodos privados (CRUD completo)
        - Métodos educativos (index, show, progress)
        - Gestión de papelera
      - ✅ AreaSearchController:
        - Hereda de SearchController base
        - Búsqueda en name y description
        - Restricciones por rol
      - ✅ AreaTrashedSearchController:
        - Similar a AreaSearchController
        - Específico para elementos eliminados

   2. **Controladores de Paths**
      - ❌ No existe PathController
      - ❌ No existe PathSearchController
      - ❌ No existe PathTrashedSearchController

   ### Acciones Necesarias

   1. **Crear PathController**
   ```php
   class PathController extends BaseController
   {
       use AuthorizesRequests, ValidatesRequests;
       
       protected $pathService;

       public function __construct(PathService $pathService)
       {
           $this->pathService = $pathService;
           
           // Middlewares como en AreaController
           $this->middleware(['auth', 'role:admin'])->only([
               'privateIndex', 'privateShow', 'privateCreate', 'privateStore', 
               'privateEdit', 'privateUpdate', 'privateDestroy',
               'privateTrashed', 'privateRestore', 'privateForceDelete'
           ]);
           
           $this->middleware(['auth'])->only(['educaIndex', 'educaShow', 'educaProgress']);
       }

       // Implementar métodos siguiendo el patrón de AreaController:
       // - publicIndex, publicShow
       // - privateIndex, privateShow, privateCreate, privateStore, privateEdit, privateUpdate, privateDestroy
       // - privateTrashed, privateRestore, privateForceDelete
       // - educaIndex, educaShow, educaProgress
   }
   ```

   2. **Crear PathSearchController**
   ```php
   class PathSearchController extends SearchController
   {
       protected function getModelClass(): string
       {
           return Path::class;
       }

       protected function getSearchFields(): array
       {
           return ['name', 'description'];
       }

       protected function formatSuggestion($model): string
       {
           return $model->name;
       }

       protected function additionalConstraints($query)
       {
           if (!auth()->user()?->hasRole('admin')) {
               $query->where('status', 'published');
           }
           return $query;
       }
   }
   ```

   3. **Crear PathTrashedSearchController**
   ```php
   class PathTrashedSearchController extends PathSearchController
   {
       protected function additionalConstraints($query)
       {
           return parent::additionalConstraints($query)->onlyTrashed();
       }
   }
   ```

   ### Notas
   - Se mantiene la misma estructura que Areas
   - Se necesita crear PathService para la lógica de negocio
   - Importante mantener la consistencia en nombres y métodos
   - Los controladores de búsqueda heredan de SearchController base
   - Se mantienen las mismas restricciones de roles y permisos

5. **Gestión de Medios**
   - Configurar integración con Spatie Media Library
   - Definir colecciones de medios, path necesita un cover como Areas, reaprovechar si es posible
   - Implementar conversiones de imágenes
   - Configurar almacenamiento

   ## 5. Análisis de Gestión de Medios

   ### Estado Actual

   1. **Configuración en Areas**
      - ✅ Usa HasMediaTrait que incluye:
        - Colección 'default' para medios generales
        - Colección 'cover' para imágenes de portada
        - Colección 'files' para archivos
        - Conversiones automáticas (thumb, medium, large)
      - ✅ Configuración en config/media.php:
        - Límites de tamaño configurables
        - Tipos de archivo permitidos
        - Dimensiones de conversiones

   2. **Configuración en Paths**
      - ❌ Usa InteractsWithMedia directamente (debe usar HasMediaTrait)
      - ❌ No tiene método registerCoverMediaCollection()
      - ❌ No tiene configuraciones específicas
      - ✅ Ya implementa HasMedia interface

   ### Acciones Necesarias

   1. **Actualizar Trait en Path**
   ```php
   // En Path.php
   use App\Traits\HasMediaTrait;
   
   class Path extends Model implements HasMedia
   {
       use HasFactory, SoftDeletes, HasMediaTrait, GeneratesSlug;
       // Eliminar use InteractsWithMedia
   ```

   2. **Implementar Método para Cover**
   ```php
   /**
    * Register the cover media collection for this model
    */
   public function registerCoverMediaCollection(): void
   {
       // Este método vacío es suficiente para activar la colección 'cover' en HasMediaTrait
   }
   ```

   3. **Configurar Limpieza de Medios**
   ```php
   protected static function booted()
   {
       parent::booted();
       static::deleting(function ($path) {
           $path->clearMediaCollection('cover');
       });
   }
   ```

   ### Notas
   - No se necesita configuración adicional en config/media.php
   - Se reutiliza HasMediaTrait completo de Areas
   - Las conversiones de imágenes son automáticas
   - El almacenamiento usa el disco 'public' por defecto
   - Los límites y tipos de archivo son los mismos que Areas
   - La gestión de usuarios de medios viene incluida en el trait

6. **Validaciones y Reglas de Negocio**
   - Crear PathRequest
   - Implementar validaciones específicas
   - Gestionar reglas de ordenamiento
   - Validar relaciones con áreas

   ## 6. Análisis de Validaciones y Reglas de Negocio

   ### Estado Actual

   1. **Validaciones en Areas (implementadas)**
      - ✅ AreaRequest con reglas completas:
        - Validación de campos básicos (name, description)
        - Validación de jerarquía (parent_id)
        - Validación de estado y featured
        - Validación de medios (cover)
        - Validación de metadatos SEO
      - ✅ Reglas de negocio:
        - Prevención de referencias circulares
        - Nombres únicos con ignore en updates
        - Preparación de datos booleanos

   2. **Validaciones en Paths**
      - ❌ No existe PathRequest
      - ❌ No hay validaciones implementadas

   ### Acciones Necesarias

   1. **Crear PathRequest**
   ```php
   class PathRequest extends FormRequest
   {
       public function authorize(): bool
       {
           return true; // La autorización se maneja en el middleware
       }

       public function rules(): array
       {
           $rules = [
               'name' => ['required', 'string', 'min:3', 'max:255'],
               'description' => ['nullable', 'string', 'max:1000'],
               'area_id' => ['required', 'exists:areas,id'],
               'parent_id' => [
                   'nullable',
                   'exists:paths,id',
                   function ($attribute, $value, $fail) {
                       if ($value) {
                           // Evitar que un path sea su propio padre
                           if ($this->route('path') && $value == $this->route('path')->id) {
                               $fail('Un path no puede ser su propio padre.');
                           }
                           // Evitar ciclos en la jerarquía
                           if ($this->route('path') && $this->hasCircularReference($this->route('path'), $value)) {
                               $fail('No se pueden crear referencias circulares en la jerarquía.');
                           }
                       }
                   }
               ],
               'featured' => ['boolean'],
               'status' => ['required', Rule::in(['draft', 'published', 'suspended'])],
               'sort_order' => ['nullable', 'integer', 'min:0'],
               'cover' => [
                   'nullable',
                   'file',
                   'mimes:' . implode(',', config('media.cover.allowed_types')),
                   'max:' . config('media.cover.max_file_size'),
                   'dimensions:max_width=' . config('media.cover.max_dimensions') . ',max_height=' . config('media.cover.max_dimensions')
               ],
               'meta' => ['nullable', 'array'],
               'meta.title' => ['nullable', 'string', 'max:60'],
               'meta.description' => ['nullable', 'string', 'max:160'],
               'meta.keywords' => ['nullable', 'string', 'max:255'],
           ];

           // Validación de nombre único dentro del área
           if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
               $rules['name'][] = Rule::unique('paths')
                   ->where('area_id', $this->area_id)
                   ->ignore($this->route('path'));
           } else {
               $rules['name'][] = Rule::unique('paths')
                   ->where('area_id', $this->area_id);
           }

           return $rules;
       }

       protected function hasCircularReference($path, $newParentId): bool
       {
           if ($path->id === $newParentId) {
               return true;
           }
           return in_array($newParentId, $path->children()->pluck('id')->toArray());
       }

       protected function prepareForValidation(): void
       {
           if ($this->has('featured')) {
               $this->merge([
                   'featured' => $this->featured === 'true' || $this->featured === '1' || $this->featured === true,
               ]);
           }
       }
   }
   ```

   ### Notas
   - Se mantiene la misma estructura que Areas
   - Se añade validación obligatoria de area_id
   - Los nombres son únicos dentro de cada área
   - Se mantienen las mismas validaciones para medios y metadatos
   - La jerarquía funciona igual que en Areas
   - No se añaden validaciones adicionales para mantener la simplicidad

7. **Servicios y Helpers**
   - Estos puntos crearlos solo si existen en Areas o si fueran imprescindibles
   - Crear PathService
   - Implementar helpers de ordenamiento
   - Desarrollar funciones de utilidad

   ## 7. Análisis de Servicios y Helpers

   ### Estado Actual

   1. **Servicios en Areas**
      - ✅ AreaService implementado con:
        - Métodos CRUD (create, update, delete)
        - Gestión de transacciones DB
        - Manejo de medios (cover)
        - Gestión de ordenamiento
        - Reordenamiento automático
      - ✅ Funcionalidades principales:
        - Control de jerarquía
        - Ordenamiento de elementos
        - Manejo de errores y logs

   2. **Servicios en Paths**
      - ❌ No existe PathService
      - ❌ No hay helpers implementados

   ### Acciones Necesarias

   1. **Crear PathService**
   ```php
   class PathService
   {
       public function create(array $data): Path
       {
           try {
               DB::beginTransaction();

               if (!isset($data['sort_order'])) {
                   $data['sort_order'] = $this->getNextSortOrder($data['parent_id'] ?? null, $data['area_id']);
               }

               $path = Path::create($data);

               if (isset($data['image'])) {
                   $path->addMediaFromRequest('image')
                        ->toMediaCollection('cover');
               }

               DB::commit();
               return $path;

           } catch (\Exception $e) {
               DB::rollBack();
               Log::error('Error creando path: ' . $e->getMessage());
               throw $e;
           }
       }

       public function update(Path $path, array $data): Path
       {
           try {
               DB::beginTransaction();

               if (isset($data['parent_id']) && $data['parent_id'] !== $path->parent_id) {
                   $data['sort_order'] = $this->getNextSortOrder($data['parent_id'], $path->area_id);
               }

               $path->update($data);

               if (isset($data['image'])) {
                   $path->clearMediaCollection('cover');
                   $path->addMediaFromRequest('image')
                        ->toMediaCollection('cover');
               }

               DB::commit();
               return $path;

           } catch (\Exception $e) {
               DB::rollBack();
               Log::error('Error actualizando path: ' . $e->getMessage());
               throw $e;
           }
       }

       public function delete(Path $path): bool
       {
           try {
               if ($path->children()->count() > 0) {
                   throw new \Exception('No se puede eliminar un path que tiene sub-paths.');
               }

               DB::beginTransaction();
               $this->reorderSiblings($path);
               $path->delete();
               DB::commit();
               return true;

           } catch (\Exception $e) {
               DB::rollBack();
               Log::error('Error eliminando path: ' . $e->getMessage());
               throw $e;
           }
       }

       protected function getNextSortOrder(?int $parentId, int $areaId): int
       {
           return Path::where('parent_id', $parentId)
                     ->where('area_id', $areaId)
                     ->max('sort_order') + 1;
       }

       protected function reorderSiblings(Path $path): void
       {
           Path::where('parent_id', $path->parent_id)
               ->where('area_id', $path->area_id)
               ->where('sort_order', '>', $path->sort_order)
               ->decrement('sort_order');
       }

       public function updateOrder(array $orderedIds, ?int $parentId = null, int $areaId): void
       {
           try {
               DB::beginTransaction();

               foreach ($orderedIds as $index => $id) {
                   Path::where('id', $id)->update([
                       'sort_order' => $index,
                       'parent_id' => $parentId
                   ]);
               }

               DB::commit();

           } catch (\Exception $e) {
               DB::rollBack();
               Log::error('Error actualizando orden de paths: ' . $e->getMessage());
               throw $e;
           }
       }
   }
   ```

   ### Notas
   - Se mantiene la misma estructura que AreaService
   - Se añade área_id en las operaciones de ordenamiento
   - Se mantiene la gestión de transacciones y manejo de errores
   - No se añaden métodos adicionales para mantener la simplicidad
   - La gestión de medios es idéntica a Areas

8. **Sistema de Búsqueda**
   - Copiar lo que se ha hecho con Areas, aprovechando lo que se pueda.
   - Implementar búsqueda con sugerencias
   - Configurar filtros específicos
   - Integrar con componente search-autocomplete

   ## 8. Análisis del Sistema de Búsqueda

   ### Estado Actual

   1. **Búsqueda en Areas**
      - ✅ SearchController abstracto base implementado:
        - Método suggestions() genérico
        - Gestión de errores y logging
        - Límite de 10 resultados
        - Búsqueda por LIKE en campos configurables
      - ✅ AreaSearchController implementado:
        - Búsqueda en campos name y description
        - Filtro por status para no-admin
        - Formato de sugerencias simple (nombre)
      - ✅ AreaTrashedSearchController implementado:
        - Misma funcionalidad + onlyTrashed()
        - Mismo sistema de permisos

   2. **Búsqueda en Paths**
      - ❌ No existe PathSearchController
      - ❌ No existe PathTrashedSearchController

   ### Acciones Necesarias

   1. **Crear PathSearchController**
   ```php
   class PathSearchController extends SearchController
   {
       protected function getModelClass(): string
       {
           return Path::class;
       }

       protected function getSearchFields(): array
       {
           return ['name', 'description'];
       }

       protected function formatSuggestion($model): string
       {
           return $model->name;
       }

       protected function additionalConstraints($query)
       {
           // Si el usuario no es admin, solo mostrar paths publicados
           if (!auth()->user()?->hasRole('admin')) {
               $query->where('status', 'published');
           }

           return $query;
       }
   }
   ```

   2. **Crear PathTrashedSearchController**
   ```php
   class PathTrashedSearchController extends PathSearchController
   {
       protected function additionalConstraints($query)
       {
           return parent::additionalConstraints($query)->onlyTrashed();
       }
   }
   ```

   ### Notas
   - Se mantiene exactamente la misma estructura que Areas
   - Se reutiliza el SearchController base sin modificaciones
   - Los campos de búsqueda son los mismos (name, description)
   - Se mantiene el mismo sistema de permisos
   - No se añaden filtros adicionales para mantener la simplicidad

9. **Vistas y Componentes**
   - Copiar las vistas y componentes de Areas, aprovechando lo que se pueda.
   - Desarrollar vistas administrativas

   ## 9. Análisis de Vistas y Componentes

   ### Estado Actual

   1. **Vistas en Areas**
      - ✅ Vistas públicas:
        - index.blade.php (listado principal)
        - show.blade.php (detalle de área)
        - teacher.blade.php (vista profesor)
        - progress.blade.php (progreso)
      - ✅ Vistas administrativas:
        - admin/areas/index.blade.php (CRUD)
        - admin/areas/create.blade.php
        - admin/areas/edit.blade.php
        - admin/areas/show.blade.php
        - admin/areas/form.blade.php (formulario reutilizable)
        - admin/areas/trashed.blade.php
      - ✅ Componentes:
        - area-actions.blade.php (botones de acción)
        - area-status.blade.php (badge de estado)
        - media/area-image-upload.blade.php

   2. **Vistas en Paths**
      - ❌ No existen vistas públicas
      - ❌ No existen vistas administrativas
      - ❌ No existen componentes específicos

   ### Acciones Necesarias

   1. **Crear Vistas Públicas**
   ```blade
   <!-- paths/index.blade.php -->
   <x-app-layout>
       <div class="py-12">
           <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
               <!-- Similar a areas/index.blade.php -->
               <!-- Añadir filtro por área -->
           </div>
       </div>
   </x-app-layout>

   <!-- paths/show.blade.php -->
   <x-app-layout>
       <div class="py-12">
           <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
               <!-- Similar a areas/show.blade.php -->
               <!-- Mostrar área padre -->
           </div>
       </div>
   </x-app-layout>
   ```

   2. **Crear Vistas Administrativas**
   ```blade
   <!-- admin/paths/index.blade.php -->
   <x-app-layout>
       <div class="py-12">
           <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
               <!-- Similar a admin/areas/index.blade.php -->
               <!-- Añadir filtro por área -->
           </div>
       </div>
   </x-app-layout>

   <!-- admin/paths/form.blade.php -->
   <form method="POST" enctype="multipart/form-data" class="space-y-6">
       <!-- Similar a admin/areas/form.blade.php -->
       <!-- Añadir selector de área obligatorio -->
       <div class="mt-4">
           <x-input-label for="area_id" :value="__('Área')" />
           <x-required-mark />
           <select id="area_id" name="area_id" required>
               <option value="">Selecciona un área</option>
               @foreach($areasList as $areaId => $areaName)
                   <option value="{{ $areaId }}">{{ $areaName }}</option>
               @endforeach
           </select>
       </div>
   </form>
   ```

   3. **Crear Componentes**
   ```blade
   <!-- components/path-actions.blade.php -->
   @props(['path', 'renderModal' => true])
   <!-- Similar a area-actions.blade.php -->

   <!-- components/path-status.blade.php -->
   @props(['status'])
   <!-- Similar a area-status.blade.php -->

   <!-- components/media/path-image-upload.blade.php -->
   @props(['path', 'label' => 'Imagen'])
   <!-- Similar a area-image-upload.blade.php -->
   ```

   ### Notas
   - Se mantiene la misma estructura y diseño que Areas
   - Se añade selector de área obligatorio en formularios
   - Se mantienen los mismos componentes pero para Paths
   - Se reutilizan los mismos layouts y componentes base
   - No se añaden vistas adicionales para mantener simplicidad

10. **Integración con Áreas**
    - Implementar relaciones bidireccionales
    - Gestionar dependencias
    - Configurar cascada de eventos
    - Validar integridad referencial

    ## 10. Análisis de Integración con Áreas

    ### Estado Actual
    1. **Relaciones Definidas**
       - ✅ Area tiene relación hasMany con Path
       - ❌ Path no tiene relación belongsTo con Area
       - ❌ No hay eventos configurados
       - ❌ No hay validación de integridad

    ### Acciones Necesarias

    1. **Relaciones en Modelos**
    ```php
    // Path.php
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
    ```

    2. **Eventos y Observadores**
    ```php
    // PathObserver.php
    public function creating(Path $path)
    {
        // Validar que el área existe y está activa
        if (!Area::where('id', $path->area_id)->where('status', 'published')->exists()) {
            throw new \Exception('El área seleccionada no existe o no está activa');
        }
    }
    ```

    3. **Gestión de Dependencias**
    ```php
    // Area.php
    protected static function booted()
    {
        // Impedir eliminación si hay paths
        static::deleting(function($area) {
            if ($area->paths()->count() > 0) {
                throw new \Exception('No se puede eliminar un área que tiene paths');
            }
        });
    }
    ```

    ### Notas
    - La relación es simple: un Path pertenece a un Area
    - No se permiten paths huérfanos (area_id es obligatorio)
    - No se permite eliminar áreas con paths, comprobar si esto se está controlando en Areas
    - No se implementa eliminación en cascada para evitar pérdida accidental de datos

    ### Puntos Anteriores Afectados

    1. **Migraciones (Punto 1)**
       - Añadir índice en area_id
       - Añadir restricción de clave foránea

    2. **Modelo (Punto 2)**
       - Añadir relación belongsTo
       - Implementar observer

    3. **Validaciones (Punto 6)**
       - Añadir validación de área existente y activa
       - Validar integridad en cambios de área

    4. **Servicios (Punto 7)**
       - Modificar PathService para validar área
       - Gestionar errores de integridad

    5. **Vistas (Punto 9)**
       - Mostrar área relacionada en vistas
       - Filtrar paths por área

Los puntos siguientes se dejarán para más adelante, no desarrollar.

11. **Tests**
    - Crear tests unitarios
    - Implementar tests de integración
    - Desarrollar tests de características
    - Validar casos especiales

12. **Documentación**
    - Documentar API
    - Crear guías de uso
    - Documentar reglas de negocio
    - Actualizar README



Nota: Este documento será expandido con el análisis detallado de cada paso cuando se requiera.
