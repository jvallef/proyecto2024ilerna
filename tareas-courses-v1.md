# Registro de Tareas del Módulo Courses

Este documento registra los prompts utilizados para cada tarea del módulo Courses, junto con las dificultades encontradas y las soluciones aplicadas.

## Pasos Previos Genéricos
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

## Tarea 1: Migración de Base de Datos
Fecha: 2024-12-05

### Prompt Utilizado

```code
CONTEXTO INMEDIATO:
- Archivos existentes:
  * database/migrations/2024_10_13_181230_create_courses_table.php (revisar y modificar si necesario)
  * database/migrations/2024_10_13_181240_create_course_path_table.php (revisar impacto)
  * database/migrations/2024_10_13_181241_create_content_course_table.php (revisar impacto)
  * database/migrations/2024_10_20_095545_create_course_enrollments_table.php (revisar impacto)
- Funcionalidad: Estructura de base de datos para Courses
- Campos requeridos:
  * Básicos: id, title, slug, description, author_id, featured, age_group, status
  * Timestamps: created_at, updated_at, deleted_at
- Dependencias:
  * users (tabla)
  * paths (tabla)
  * contents (tabla)
- Validaciones DB:
  * slug único
  * age_group enum válido
  * status enum válido
  * claves foráneas con restrict

RESTRICCIONES:
- NO crear campos adicionales
- NO modificar comportamiento de soft deletes
- MANTENER estructura similar a paths
- USAR SOLO tipos de datos estándar de Laravel
- RESPETAR orden de campos
- SEGUIR convenciones de nombres de Laravel

REFERENCIA:
- Implementación base: 
  * database/migrations/2024_10_13_181225_create_paths_table.php
  * database/migrations/2024_11_23_151806_add_author_status_to_courses_table.php
- Documentación: analisis-paths-v1.md (adaptar para courses)

TAREA:
- Acción: Revisar y ajustar las migraciones existentes
- Resultado: Tablas courses y relaciones completamente configuradas
- Dependencias: users, paths y contents tables deben existir
- Comprobar si existen seeders de courses y sino crearlos siguiendo el patrón de areas y paths.

VERIFICACIÓN:
- Comprobar índices necesarios
- Validar restricciones de claves foráneas
- Tests: 
  * migrate:fresh funciona
  * rollback funciona
  * referencias circulares prevenidas
- Integridad:
  * Relaciones con paths y contents correctas
  * Soft delete funcional
  * Enrollments configurados correctamente
```

### Dificultades y Soluciones

1. Dificultad: Gestión de relaciones múltiples
   - Descripción: Complejidad en la gestión de relaciones many-to-many con paths y contents
   - Solución: Implementar tablas pivot con timestamps y soft deletes

2. Dificultad: Manejo de estados del curso
   - Descripción: Necesidad de controlar la visibilidad según el estado
   - Solución: Implementar enum status con valores y restricciones apropiadas

3. Dificultad: Sistema de matrículas
   - Descripción: Complejidad en la gestión de enrollments con estados y fechas
   - Solución: Tabla específica con campos de control y estados

## Tarea 2: Model Course
Fecha: 2024-12-05

### Prompt Utilizado

```code
CONTEXTO INMEDIATO:
- Archivos a modificar: 
  * app/Models/Course.php (modificar)
  * app/Services/CourseService.php (crear)
- Funcionalidad actual:
  * Modelo base con relaciones y traits básicos
  * Gestión de cursos y matrículas
  * Integración con Paths y Media
- Campos/métodos/relaciones:
  * Campos DB: id, title, slug, description, author_id, featured, age_group, status
  * Relaciones: belongsTo(User), belongsToMany(Path, Content), hasMany(Enrollment)
  * Traits: HasFactory, SoftDeletes, HasMedia
- Dependencias:
  * Spatie Media Library
  * Laravel Framework
  * Modelos Path, Content y User
- Validaciones existentes:
  * Unique slug
  * Required fields (title, author_id)
  * Valid status and age_group values

RESTRICCIONES:
- NO crear campos adicionales en la base de datos
- NO modificar la estructura de las relaciones existentes
- MANTENER compatibilidad con Path model
- USAR SOLO traits existentes en el proyecto
- RESPETAR convenciones de nombres
- SEGUIR patrones establecidos

REFERENCIA:
- Implementación base: 
  * app/Models/Path.php
  * app/Services/PathService.php
- Documentación: 
  * spatie/laravel-medialibrary docs
- Tests relacionados:
  * tests/Unit/PathTest.php (adaptar para Course)

TAREA:
- Acción: Implementar Course model y service
- Resultado: Modelo funcional con todas las relaciones y service para gestión
- Dependencias: 
  * Migraciones completadas
  * Path model existente
  * Media library instalada

VERIFICACIÓN:
- Comprobar:
  * Todas las relaciones están definidas
  * Service implementa CRUD completo
  * Media library está configurada
- Validar:
  * Generación de slugs
  * Gestión de media
  * Soft deletes
  * Sistema de matrículas
- Tests necesarios:
  * Creación de courses
  * Relaciones (paths, contents, enrollments)
  * Media attachments
- Integridad:
  * Gestión correcta de relaciones
  * Limpieza de media al eliminar
```

### Dificultades y Soluciones

1. Dificultad: Gestión de matrículas
   - Descripción: Complejidad en la lógica de enrollments y sus estados
   - Solución: Implementar métodos específicos en CourseService

2. Dificultad: Relaciones múltiples
   - Descripción: Gestión de relaciones con paths y contents
   - Solución: Implementar métodos helper para gestionar las relaciones

## Tarea 3: Sistema de Rutas para Courses
Fecha: 2024-12-05

### Prompt Utilizado

```code
CONTEXTO INMEDIATO:
- Archivos a modificar: 
  * routes/web.php (modificar)
  * routes/api.php (no funciona, rutas de apis crear en web.php como las de Paths)
- Funcionalidad actual, copiar de Paths:
  * Rutas públicas de cursos
  * Rutas administrativas de cursos
  * Rutas de API para búsqueda
  * Rutas de workarea (teacher/student)
- Endpoints necesarios:
  * GET /courses (public, admin, workarea)
  * GET /courses/{slug} (public, workarea)
  * GET /courses/trashed (admin)
  * CRUD completo en admin
  * Búsqueda y sugerencias
  * Endpoints de matrícula
- Dependencias:
  * CourseController
  * CourseSearchController
  * CourseTrashedSearchController
  * Middleware de roles
- Validaciones existentes:
  * Autenticación
  * Roles (admin, teacher, student)
  * Slugs únicos

RESTRICCIONES:
- NO crear nuevos grupos de rutas
- NO modificar rutas existentes
- MANTENER la misma estructura de Paths
- USAR SOLO middleware existentes
- RESPETAR convenciones de nombres
- SEGUIR patrones establecidos

REFERENCIA:
- Implementación base: 
  * routes/web.php (ver rutas de Paths)
- Documentación: 
  * analisis-paths-v1.md (adaptar para courses)

TAREA:
- Acción: Implementar rutas para Courses
- Resultado: Sistema completo de rutas siguiendo Paths
- Dependencias: 
  * Controllers creados
  * Middleware configurados
  * Roles definidos

VERIFICACIÓN:
- Comprobar:
  * Todas las rutas necesarias están definidas
  * Nombres de rutas son consistentes
  * Middleware están aplicados correctamente
- Validar:
  * Acceso público vs privado
  * Permisos por rol
  * Rutas de búsqueda y matrícula
- Tests necesarios:
  * Acceso a rutas públicas
  * Acceso a rutas privadas
  * Búsqueda y matrículas
```

### Dificultades y Soluciones

1. Dificultad: Rutas de matrícula
   - Descripción: Necesidad de endpoints específicos para gestión de matrículas
   - Solución: Crear grupo de rutas específico para enrollments

2. Dificultad: Control de acceso
   - Descripción: Diferentes niveles de acceso según rol y estado de matrícula
   - Solución: Implementar middleware específico para verificar matrícula

## Tarea 4: Controladores para Courses
Fecha: 2024-12-05

### Prompt Utilizado

```code
CONTEXTO INMEDIATO:
- Archivos a modificar/crear: 
  * app/Http/Controllers/CourseController.php (crear)
  * app/Http/Controllers/Api/CourseSearchController.php (crear)
  * app/Http/Controllers/Api/CourseTrashedSearchController.php (crear)
  * app/Http/Controllers/CourseEnrollmentController.php (crear)
- Funcionalidad actual:
  * Rutas definidas para courses
  * CourseService implementado
  * Modelo Course configurado
- Métodos necesarios:
  * Públicos: index, show
  * Privados: index, show, create, store, edit, update, destroy, trashed, restore, forceDelete
  * Educativos: index, show, progress
  * Matrículas: enroll, unenroll, progress
  * Búsqueda: suggestions
- Dependencias:
  * CourseService
  * CourseRequest (pendiente)
  * EnrollmentService
  * Middleware de autenticación y roles

RESTRICCIONES:
- NO crear nuevos middleware
- NO modificar lógica de negocio (usar CourseService)
- MANTENER estructura de PathController
- USAR SOLO middleware existentes
- RESPETAR convenciones de nombres
- SEGUIR patrones establecidos

REFERENCIA:
- Implementación base: 
  * app/Http/Controllers/PathController.php
  * app/Http/Controllers/Api/PathSearchController.php
- Documentación: 
  * analisis-paths-v1.md (adaptar para courses)

TAREA:
- Acción: Implementar controladores para Courses
- Resultado: Sistema completo de controladores siguiendo Paths
- Dependencias: 
  * CourseService implementado
  * Rutas definidas
  * Middleware configurados

VERIFICACIÓN:
- Comprobar:
  * Todos los métodos necesarios implementados
  * Middleware aplicados correctamente
  * Gestión de matrículas funcional
- Validar:
  * Manejo de errores
  * Respuestas consistentes
  * Uso correcto de CourseService
- Tests necesarios:
  * Métodos públicos
  * Métodos privados
  * Matrículas y progreso
```

### Dificultades y Soluciones

1. Dificultad: Gestión de progreso
   - Descripción: Necesidad de tracking del progreso del estudiante
   - Solución: Implementar sistema de progreso en EnrollmentService

2. Dificultad: Estados de matrícula
   - Descripción: Diferentes estados posibles en las matrículas
   - Solución: Implementar enum y métodos de control en EnrollmentService

## Tarea 5: Gestión de Medios
Fecha: 2024-12-05

### Prompt Utilizado

```code
CONTEXTO INMEDIATO:
- Archivos a revisar y/o modificar: 
  * app/Models/Course.php
  * app/Traits/HasMediaTrait.php (verificar)
  * config/media.php (verificar)
- Funcionalidad actual:
  * Course usa InteractsWithMedia
  * HasMediaTrait implementado en Paths
  * Configuraciones base en config/media.php
- Colecciones necesarias:
  * 'cover' para imágenes de portada
  * 'banner' para imágenes de cabecera
  * 'files' para materiales del curso
  * 'default' para medios generales
- Conversiones requeridas:
  * thumb (200x200)
  * medium (800x600)
  * large (1200x900)
  * banner (1920x400)
- Dependencias:
  * Spatie Media Library
  * HasMediaTrait
  * Configuración de almacenamiento
  * Validaciones de archivos

RESTRICCIONES:
- NO modificar HasMediaTrait existente
- NO alterar configuraciones globales
- MANTENER consistencia con Paths
- USAR SOLO colecciones definidas
- RESPETAR límites de tamaño
- SEGUIR convenciones de nombres

REFERENCIA:
- Implementación base: 
  * app/Models/Path.php
  * app/Traits/HasMediaTrait.php
- Documentación: 
  * spatie/laravel-medialibrary docs

TAREA:
- Acción: Configurar gestión de medios para Courses
- Resultado: Sistema de medios completo y consistente
- Dependencias: 
  * HasMediaTrait implementado
  * Spatie configurado
  * Almacenamiento configurado

VERIFICACIÓN:
- Comprobar:
  * Trait aplicado correctamente
  * Colecciones registradas
  * Conversiones configuradas
- Validar:
  * Subida de archivos
  * Conversiones automáticas
  * Limpieza de archivos
- Tests necesarios:
  * Subida de medios
  * Conversiones
  * Eliminación
```

### Dificultades y Soluciones

1. Dificultad: Materiales del curso
   - Descripción: Necesidad de gestionar diferentes tipos de archivos
   - Solución: Implementar validaciones específicas por tipo de archivo

2. Dificultad: Tamaño de archivos
   - Descripción: Límites diferentes según tipo de material
   - Solución: Configurar límites específicos por colección

## Tarea 6: Validaciones y Reglas de Negocio
Fecha: 2024-12-05

### Prompt Utilizado

```code
CONTEXTO INMEDIATO:
- Archivos a crear/modificar: 
  * app/Http/Requests/CourseRequest.php (crear)
  * app/Models/Course.php (verificar)
  * app/Services/CourseService.php (verificar)
- Funcionalidad actual:
  * Modelo Course con relaciones definidas
  * CourseService implementado
  * Controladores usando Request genérico
- Validaciones necesarias:
  * Campos básicos (title, description)
  * Relaciones (author_id)
  * Estado, featured y age_group
  * Medios (cover, banner)
  * Metadatos SEO
  * Matrículas
- Reglas de negocio:
  * Títulos únicos
  * Estados válidos
  * Grupos de edad válidos
  * Relaciones con paths
  * Control de matrículas
- Dependencias:
  * FormRequest de Laravel
  * Reglas de validación existentes
  * Configuración de medios
  * CourseService

RESTRICCIONES:
- NO modificar PathRequest existente
- NO alterar reglas globales
- MANTENER consistencia con Paths
- USAR SOLO reglas estándar de Laravel
- RESPETAR límites de tamaño
- SEGUIR convenciones de nombres

REFERENCIA:
- Implementación base: 
  * app/Http/Requests/PathRequest.php
  * app/Models/Path.php
  * app/Services/PathService.php
- Documentación: 
  * analisis-paths-v1.md (adaptar para courses)
  * Laravel validation docs

TAREA:
- Acción: Implementar validaciones y reglas de negocio
- Resultado: Sistema de validación completo y consistente
- Dependencias: 
  * Modelo Course implementado
  * CourseService configurado
  * Controladores preparados

VERIFICACIÓN:
- Comprobar:
  * Todas las reglas implementadas
  * Mensajes personalizados
  * Funciones de validación custom
- Validar:
  * Títulos únicos
  * Estados válidos
  * Grupos de edad
  * Matrículas correctas
- Tests necesarios:
  * Validaciones básicas
  * Reglas de negocio
  * Casos límite
- Integridad:
  * No hay conflictos con Paths
  * Consistencia en mensajes
  * Reglas coherentes
```

### Dificultades y Soluciones

1. Dificultad: Validación de age_group
   - Descripción: Necesidad de validar valores específicos de age_group
   - Solución: Implementar enum y regla de validación específica

2. Dificultad: Control de matrículas
   - Descripción: Validaciones complejas para el sistema de matrículas
   - Solución: Crear reglas específicas para cada estado de matrícula

3. Dificultad: Mensajes específicos
   - Descripción: Necesidad de mensajes claros para cada tipo de error
   - Solución: Implementar mensajes personalizados por tipo de validación

## Tarea 7: Servicios y Helpers
Fecha: 2024-12-05

### Prompt Utilizado

```code
CONTEXTO INMEDIATO:
- Archivos a crear/modificar: 
  * app/Services/CourseService.php (crear si no existe)
  * app/Services/EnrollmentService.php (crear si no existe y si es necesario)
  * app/Helpers/CourseHelper.php (si existe el de Paths, o si es necesario)
  * app/Models/Course.php (verificar)
- Funcionalidad actual:
  * Modelo Course con relaciones definidas
  * CourseRequest implementado
  * Controladores usando Request genérico
- Servicios necesarios:
  * CRUD completo (create, update, delete)
  * Gestión de transacciones DB
  * Manejo de medios (cover, banner)
  * Gestión de matrículas
  * Sistema de progreso
- Helpers requeridos:
  * Manejo de estados
  * Control de matrículas
  * Logging de operaciones
  * Funciones de utilidad
- Dependencias:
  * Modelo Course
  * CourseRequest
  * DB Transactions
  * Media Library

RESTRICCIONES:
- NO modificar PathService existente
- NO alterar lógica global
- MANTENER consistencia con Paths
- USAR SOLO métodos estándar de Laravel
- RESPETAR transacciones DB
- SEGUIR convenciones de nombres

REFERENCIA:
- Implementación base: 
  * app/Services/PathService.php
  * app/Models/Path.php
- Documentación: 
  * analisis-paths-v1.md (adaptar para courses)
  * Laravel service docs

TAREA:
- Acción: Implementar servicios y helpers para Courses
- Resultado: Sistema de servicios completo y consistente
- Dependencias: 
  * Modelo Course implementado
  * CourseRequest configurado
  * Controladores preparados

VERIFICACIÓN:
- Comprobar:
  * Todos los métodos necesarios
  * Manejo de transacciones
  * Gestión de errores
- Validar:
  * Gestión de matrículas
  * Manejo de medios
  * Logging adecuado
- Tests necesarios:
  * Operaciones CRUD
  * Matrículas
  * Progreso
- Integridad:
  * Transacciones DB correctas
  * Manejo de errores robusto
  * Logging completo
```

### Dificultades y Soluciones

1. Dificultad: Gestión de progreso
   - Descripción: Complejidad en el seguimiento del progreso del estudiante
   - Solución: Implementar sistema de tracking en EnrollmentService

2. Dificultad: Estados de matrícula
   - Descripción: Múltiples estados posibles en el ciclo de vida de una matrícula
   - Solución: Crear enum y métodos específicos de control de estado

## Tarea 8: Implementación de la lógica de negocio
Fecha: 2024-12-05

### Prompt Utilizado

```code
CONTEXTO INMEDIATO:
- Archivos a crear/modificar: 
  * app/Services/CourseService.php (modificar, si fuera preciso)
  * app/Services/EnrollmentService.php (modificar, si existe y/o fuera preciso)
  * app/Models/Course.php (verificar)
  * app/Http/Controllers/CourseController.php (verificar)
- Funcionalidad actual:
  * Modelo Course con relaciones definidas
  * CourseRequest implementado
  * Controladores usando Request genérico
- Lógica de negocio necesaria:
  * Gestión de cursos y contenidos
  * Integración con Paths y Media
  * Sistema de matrículas
  * Control de progreso
  * Estados y transiciones
- Dependencias:
  * Modelo Course
  * CourseRequest
  * DB Transactions
  * Media Library

RESTRICCIONES:
- NO modificar PathService existente
- NO alterar lógica global
- MANTENER consistencia con Paths
- USAR SOLO métodos estándar de Laravel
- RESPETAR transacciones DB
- SEGUIR convenciones de nombres

REFERENCIA:
- Implementación base: 
  * app/Services/PathService.php
  * app/Models/Path.php
  * app/Http/Controllers/PathController.php
- Documentación: 
  * analisis-paths-v1.md (adaptar para courses)
  * Laravel service docs

TAREA:
- Acción: Implementar lógica de negocio para Courses
- Resultado: Sistema de lógica de negocio completo y consistente
- Dependencias: 
  * Modelo Course implementado
  * CourseRequest configurado
  * Controladores preparados

VERIFICACIÓN:
- Comprobar:
  * Todos los métodos necesarios
  * Manejo de transacciones
  * Gestión de errores
- Validar:
  * Gestión de cursos y contenidos
  * Integración con Paths y Media
  * Sistema de matrículas
  * Control de progreso
  * Estados y transiciones
- Tests necesarios:
  * Operaciones CRUD
  * Gestión de matrículas
  * Control de progreso
  * Casos de error
- Integridad:
  * No hay conflictos con Paths
  * Transacciones completas
  * Rollback funcionando
```

### Dificultades y Soluciones

1. Dificultad: Estados de matrícula
   - Descripción: Complejidad en la gestión de estados y transiciones
   - Solución: Implementar máquina de estados con validaciones

2. Dificultad: Control de progreso
   - Descripción: Necesidad de tracking preciso del avance del estudiante
   - Solución: Sistema de checkpoints y validaciones de progreso

3. Dificultad: Relaciones múltiples
   - Descripción: Gestión compleja de relaciones con paths y contents
   - Solución: Implementar métodos específicos para cada tipo de relación

## Tarea 9: Vistas y Componentes
Fecha: 2024-12-05

⚠️ **ADVERTENCIA IMPORTANTE**
Esta tarea requiere una atención especial a la consistencia y replicación exacta del patrón de Paths. En implementaciones anteriores, las desviaciones del patrón establecido han causado problemas de integración y funcionalidad. Es CRÍTICO:
1. NO innovar ni añadir funcionalidades no presentes en Paths
2. COPIAR exactamente la estructura y nombrado de archivos
3. MANTENER la misma jerarquía de componentes
4. REPLICAR los mismos patrones de diseño y UX
5. USAR los mismos componentes base

### Prompt Utilizado

```code
CONTEXTO INMEDIATO:
- Archivos a crear/modificar: 
  * resources/views/courses/*.blade.php (crear)
  * resources/views/admin/courses/*.blade.php (crear)
  * resources/views/educa/courses/*.blade.php (crear)
  * resources/views/components/courses/*.blade.php (crear)
- Vistas existentes de referencia:
  * resources/views/paths/*.blade.php
  * resources/views/admin/paths/*.blade.php
  * resources/views/educa/paths/*.blade.php
  * resources/views/components/paths/*.blade.php
- Funcionalidad actual:
  * Controladores implementados
  * Rutas definidas
  * Servicios configurados
- Vistas necesarias:
  * Listados (index, trashed)
  * Formularios (create, edit)
  * Detalles (show)
  * Componentes específicos
  * Vistas de matrícula
  * Progreso del estudiante
- Dependencias:
  * Layouts existentes
  * Componentes base
  * Assets y estilos
  * Scripts JS

RESTRICCIONES:
- ⚠️ NO modificar vistas de Paths existentes
- ⚠️ NO alterar componentes base
- ⚠️ NO añadir funcionalidades extra
- ⚠️ MANTENER exactamente la misma estructura
- ⚠️ USAR los mismos nombres de archivo
- ⚠️ REPLICAR la misma jerarquía
- SEGUIR convenciones de Blade
- RESPETAR organización de assets

REFERENCIA:
- Implementación base: 
  * Todas las vistas de Paths
  * Componentes de Paths
  * Layouts existentes
- Documentación: 
  * analisis-paths-v1.md (adaptar para courses)
  * Laravel Blade docs
- Archivos relacionados:
  * resources/js/app.js
  * resources/css/app.css
  * webpack.mix.js

TAREA:
- Acción: Implementar vistas y componentes para Courses
- Resultado: Sistema de vistas completo y consistente
- Dependencias: 
  * Controladores implementados
  * Rutas configuradas
  * Servicios funcionando

VERIFICACIÓN:
- Comprobar:
  * Estructura idéntica a Paths
  * Nombres de archivo coincidentes
  * Jerarquía de componentes
  * Vistas de matrícula
  * Interfaz de progreso
- Validar:
  * Formularios funcionando
  * Listados correctos
  * Componentes integrados
  * Sistema de matrículas
  * Tracking de progreso
- Tests visuales:
  * Responsive design
  * Consistencia de estilos
  * UX coherente
- Integridad:
  * No hay conflictos con Paths
  * Assets organizados
  * JS/CSS optimizado
```

### Dificultades y Soluciones

1. Dificultad: Interfaz de progreso
   - Descripción: Necesidad de mostrar el progreso del estudiante de forma clara
   - Solución: Implementar componente de progreso reutilizable

2. Dificultad: Formularios de matrícula
   - Descripción: Complejidad en la gestión de estados de matrícula
   - Solución: Crear componentes específicos para cada estado

3. Dificultad: Consistencia visual
   - Descripción: Mantener coherencia con el diseño de Paths
   - Solución: Utilizar los mismos componentes base y estilos

[Continuar con la siguiente tarea si existe...]
