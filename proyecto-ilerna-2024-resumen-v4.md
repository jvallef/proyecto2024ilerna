# Proyecto Ilerna 2024 (EduPlazza) - Resumen de Desarrollo

## 📋 Visión General del Proyecto

### Objetivo del Proyecto
- Crear un repositorio educativo jerárquico
- Gestionar contenido formativo de manera estructurada
- Implementar un sistema de usuarios con roles diferenciados

### Estado Actual
- Fase: MVP
- Backend: Laravel 11 con SQLite
- Frontend: Tailwind CSS + Laravel Breeze + Alpine.js
- Gestión de roles: Spatie Laravel-Permission
- Gestión de imágenes y archivos: Spatie Media Library
- Sistema de pruebas: PHPUnit con TestCase personalizado
- Url desarrollo: https://proyecto2024ilerna.test

### Estructura Principal
- Areas: Áreas de conocimiento
- Paths: Rutas de aprendizaje
- Courses: Cursos
- Contents: Contenidos
- Users y Roles: Sistema de usuarios y roles
- Media: Gestión de imágenes y archivos

### Estado de Implementación

#### Componentes Reutilizables
##### Search Autocomplete
- Componente: `search-autocomplete.blade.php`
- Tecnologías: Alpine.js + Laravel
- Características:
  - Búsqueda dinámica con debounce (300ms)
  - Sugerencias en tiempo real
  - Navegación por teclado (flechas arriba/abajo)
  - Formato personalizado de sugerencias (nombre + email)
  - Separador seguro || para datos compuestos
  - Botón de limpieza (x) dinámico
  - Indicador de carga
  - Soporte para búsqueda flexible (OR) en múltiples campos
- Implementación:
  ```blade
  <x-search-autocomplete
      :route="route('admin.users.index')"
      :search-url="route('admin.api.search.users')"
      :placeholder="__('Buscar usuarios...')"
      :min-chars="2"
  />
  ```

##### Avatar Upload
- Componente: `avatar-upload.blade.php`
- Características:
  - Previsualización en tiempo real
  - Validación de dimensiones y tamaño
  - Soporte para drag & drop
  - Mensajes de error personalizados
  - Límites configurables vía .env

#### Modelos Implementados
- `User`: Implementado completamente
  - Autenticación y roles
  - Avatar con trait HasAvatar
  - Búsqueda avanzada por nombre/email
  - Soft deletes
  - Validaciones completas

- `Area`: En desarrollo
  - Modelo base con relaciones (parent/children, paths, user, medias)
  - Traits implementados:
    - GeneratesSlug: Generación automática de slugs únicos
    - HasMediaTrait: Gestión de imágenes
    - SoftDeletes: Borrado suave
  - Validaciones específicas:
    - Nombre único y obligatorio
    - Jerarquía sin ciclos
    - Metadatos SEO opcionales
  - API de búsqueda implementada:
    - Endpoint: `/admin/api/search/areas`
    - Búsqueda por nombre y descripción
    - Formato de sugerencias personalizado
  - Servicio dedicado (`AreaService`):
    - Gestión de jerarquía
    - Control de ordenamiento (sort_order)
    - Manejo de metadatos
    - Gestión transaccional de operaciones

- `Content`: Archivo base creado
- `Course`: Archivo base creado
- `Media`: Implementado para gestión de avatares y archivos
- `Message`: Archivo base creado
- `Path`: Archivo base creado

#### Controladores
- `UserController`: CRUD completo con búsqueda avanzada
- `UserSearchController`: API de búsqueda con sugerencias
- `AreaController`: En desarrollo
  - Rutas administrativas configuradas
  - Middleware de autorización implementado
  - API de búsqueda con `AreaSearchController`
- `ProfileController`: Implementado para gestión de perfiles
- `ImageController`: Implementado para manejo de avatares y validación
- Controladores de autenticación en carpeta Auth

#### Sistema de Archivos y Media
- Implementación de subida de avatares con validación
- Límites configurables en .env:
  - AVATAR_MAX_FILE_SIZE=2048 (KB)
  - AVATAR_MAX_DIMENSIONS=3000 (pixels)
- Tipos de archivo permitidos: jpg, jpeg, png, webp
- Almacenamiento en disco público con nombres únicos
- Trait HasAvatar para manejo de avatares en User

#### Sistema de Testing
- Tests de Feature:
  - `UserAvatarValidationTest`: Validación completa de avatares
  - `SlugGenerationTest`: Generación de slugs para URLs
  - `ImageControllerTest`: Manejo de imágenes
  - `ProfileTest`: Funcionalidad de perfiles
  - Tests completos de autenticación (registro, login, reset, etc.)

#### API y Endpoints
- Búsqueda de Usuarios:
  - Endpoint: `/admin/api/search/users`
  - Controlador: `UserSearchController`
  - Características:
    - Búsqueda en múltiples campos
    - Formato de sugerencias personalizado
    - Separador seguro || para datos compuestos
    - Logging detallado para debugging
    - Búsqueda flexible con OR

- Búsqueda de Áreas:
  - Endpoint: `/admin/api/search/areas`
  - Controlador: `AreaSearchController`
  - Características:
    - Búsqueda por nombre y descripción
    - Formato de sugerencias personalizado
    - Límite de 10 resultados
    - Ordenamiento por nombre
    - Logging para debugging

#### Rutas y Permisos
- Rutas de autenticación configuradas
- Rutas para áreas:
  - Públicas: `/areas` (index, show)
  - Admin: `/admin/areas` (CRUD completo)
- Middleware para roles (admin, teacher, student)
- Prefijos de rutas por rol (/admin, /workarea, /classroom)
- Middleware `ensureRole` implementado
- Soporte para múltiples roles en rutas

#### Pendiente de Implementación
- Vistas administrativas para Areas
- Vistas públicas para contenido
- Implementación completa de modelos Path, Course y Content
- Sistema de mensajes (Message model)
- Pruebas unitarias adicionales para nuevos modelos

### Próximos Pasos

1. Gestión de Áreas _(En Progreso)_
   - ✅ Rutas administrativas configuradas
   - ✅ API de búsqueda implementada
   - ✅ Validaciones y reglas de negocio
   - ✅ Servicio de gestión implementado
   - 🔄 Implementar vistas administrativas
   - Implementar ordenamiento drag & drop
   - Adaptar componentes de media

2. Gestión de Rutas (Paths) _(Prioridad Media)_
   - Crear CRUD básico
   - Implementar relaciones con áreas
   - Adaptar componentes de búsqueda y media
   - Sistema de ordenación y jerarquía

3. Sistema de Archivos _(Prioridad Media)_
   - Extender sistema actual de media
   - Implementar gestión por área/ruta
   - Añadir validaciones específicas por tipo
   - Gestión de permisos por tipo de archivo

4. Mejoras de UX _(Prioridad Baja)_
   - Añadir filtros adicionales en búsquedas
   - Mejorar feedback visual
   - Optimizar rendimiento de componentes
   - Mejorar accesibilidad

## Proceso de Revisión y Refactorización de Módulos

### 1. Análisis Inicial
1. **Búsqueda de Archivos Relacionados**:
   ```bash
   # Buscar todos los archivos relacionados con el módulo
   find app -name "*Area*.php"
   find database/migrations -name "*areas*.php"
   ```
   - Revisar controladores (Public, Api, etc.)
   - Identificar modelos y relaciones
   - Localizar migraciones
   - Encontrar requests y services

2. **Revisión de Migraciones**:
   - Examinar estructura de tablas
   - Verificar tipos de campos y restricciones
   - Comprobar índices y claves foráneas
   - Validar soft deletes y timestamps
   - Asegurar consistencia con otros módulos relacionados

3. **Análisis de Modelo**:
   - Confirmar traits necesarios (HasMedia, SoftDeletes)
   - Verificar fillable/guarded
   - Revisar relaciones con otros modelos
   - Comprobar scopes y métodos auxiliares
   - Asegurar que las relaciones coinciden con migraciones

4. **Revisión de Requests**:
   - Validar reglas contra campos de migración
   - Implementar validaciones de archivos
   - Usar variables de entorno para configuración
   - Añadir mensajes de error personalizados
   - Prevenir referencias circulares si aplica

### 2. Implementación de Controladores
1. **Estructura de Métodos**:
   - public* (acceso público)
   - private* (acceso admin)
   - educa* (acceso plataforma)

2. **Configuración**:
   - Middleware y roles
   - Inyección de servicios
   - Variables de entorno (paginación, etc.)

3. **Estandarización**:
   - Manejo consistente de errores
   - Logging estructurado
   - Respuestas JSON para API
   - Redirecciones y mensajes flash

### 3. Servicios y Helpers
1. **Services**:
   - Lógica de negocio centralizada
   - Manejo de transacciones
   - Gestión de archivos/media
   - Ordenamiento y jerarquías

2. **API Controllers**:
   - Endpoints de búsqueda
   - Validaciones específicas
   - Transformación de datos

### 4. Variables de Entorno
1. **Configuración de Media**:
   ```env
   # Ejemplo para Areas
   COVER_ALLOWED_TYPES="jpg,jpeg,png,webp"
   COVER_MAX_FILE_SIZE=2048
   COVER_MAX_DIMENSIONS=2000
   ```

2. **Configuración General**:
   ```env
   PAGINATION_PER_PAGE=12
   ```

### 5. Lista de Verificación Final
- [ ] Migraciones coherentes y completas
- [ ] Modelo con traits y relaciones correctas
- [ ] Request con validaciones robustas
- [ ] Controller con métodos organizados
- [ ] Service con lógica de negocio centralizada
- [ ] API endpoints configurados
- [ ] Variables de entorno documentadas
- [ ] Rutas organizadas por contexto
- [ ] Middleware y permisos configurados
- [ ] Manejo de archivos estandarizado

## Módulos Implementados

### Areas
1. **Migraciones**:
   - create_areas_table
   - add_sorting_and_meta_to_areas_table

2. **Modelo**:
   - HasMedia para gestión de imágenes
   - SoftDeletes para papelera
   - Relaciones: user, parent, children
   - Scopes: published, featured

3. **Controller**:
   - Métodos prefijados como public para guest, private para admin y educa para teachers y students
   - Manejo de imágenes con collection 'cover'
   - Paginación configurable
   - Búsqueda integrada

4. **Servicios**:
   - AreaService: CRUD y ordenamiento
   - AreaSearchController: API de búsqueda

5. **Variables Entorno**:
   ```env
   COVER_ALLOWED_TYPES="jpg,jpeg,png,webp"
   COVER_MAX_FILE_SIZE=2048
   COVER_MAX_DIMENSIONS=2000
   PAGINATION_PER_PAGE=12
   ```

### Áreas

#### Puntos Clave para la Implementación

1. **Búsqueda**
   - Separar los controladores de búsqueda por funcionalidad (normal vs trashed)
   - Mantener la misma estructura que UserSearchController como referencia inical, pero una vez que esté funcionando AreaSearchController y TrashedAreaSearchController son una buena referenci a seguir.
   - Usar rutas con nombres consistentes: `api.areas.search` y `api.areas.trashed.search`
   - No mezclar lógica de trashed en el controlador principal de búsqueda

2. **Modales de Confirmación**
   - Usar el componente `x-modal-confirm` en lugar de `x-modal` básico tomando como referencia los de Areas cuando existan y sino el de User que es el modelo inicial.
   - Asegurarse de incluir los parámetros de paginación y búsqueda en la URL de acción
   - Estructura del modal:
     ```blade
     <x-modal-confirm
         id="modal-id-{{ $item->id }}"
         title="Título"
         message="Mensaje"
         :action="route('route.name', ['item' => $item, 'page' => request('page', 1), 'search' => request('search')])"
         confirm="Texto Confirmar"
         cancel="Texto Cancelar"
         method="DELETE"  // Solo si es necesario
     />
     ```
   - El botón que abre el modal debe usar:
     ```blade
     @click="$dispatch('open-modal', { id: 'modal-id-{{ $item->id }}' })"
     ```

3. **Vistas**
   - Mantener consistencia entre index y trashed
   - Reutilizar componentes como `x-search-autocomplete`
   - Pasar los parámetros correctos a los componentes:
     ```blade
     <x-search-autocomplete 
         :route="route('admin.areas.trashed')"
         :search-url="route('api.areas.trashed.search')"
         placeholder="Buscar por nombre..." 
     />
     ```

4. **Controladores de Búsqueda**
   - Estructura básica:
     ```php
     class AreaSearchController extends SearchController
     {
         protected function getModelClass(): string
         {
             return Area::class;
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
             // Añadir restricciones específicas aquí
             return $query;
         }
     }
     ```

#### Lecciones Aprendidas
- Mantener la lógica de trashed separada del controlador principal
- Usar componentes existentes como referencia (UserSearchController)
- Seguir patrones consistentes en rutas y nombres
- Preferir componentes predefinidos (`x-modal-confirm`) sobre implementaciones personalizadas
- Incluir siempre parámetros de paginación y búsqueda en las acciones de formularios

## 📦 Estructura de Archivos Clave
```
proyecto2024ilerna/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── ...
│   │   │   ├── Api/
│   │   │   │   ├── UserSearchController.php
│   │   │   │   └── AreaSearchController.php
│   │   │   ├── UserController.php
│   │   │   ├── ImageController.php
│   │   │   ├── ProfileController.php
│   │   │   └── AreaController.php
│   │   ├── Requests/
│   │   │   └── AreaRequest.php
│   │   └── Middleware/
│   │       └── EnsureRole.php
│   ├── Models/
│   │   ├── Area.php
│   │   ├── User.php
│   │   └── Traits/
│   │       ├── HasAvatar.php
│   │       ├── GeneratesSlug.php
│   │       └── HasMediaTrait.php
│   └── Services/
│       ├── ImageService.php
│       └── AreaService.php
├── config/
│   └── permission.php
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 2024_10_13_181220_create_areas_table.php
│   │   └── 2024_11_23_133205_add_sorting_and_meta_to_areas_table.php
│   └── seeders/
│       └── RoleSeeder.php
├── resources/
│   ├── css/
│   │   └── app.css
│   └── views/
│       ├── admin/
│       │   ├── users/
│       │   │   ├── index.blade.php
│       │   │   └── form.blade.php
│       │   └── areas/
│       │       └── ...
│       ├── components/
│       │   ├── search-autocomplete.blade.php
│       │   └── media/
│       │       └── avatar-upload.blade.php
│       └── layouts/
│           ├── app.blade.php
│           └── navigation.blade.php
```

```
