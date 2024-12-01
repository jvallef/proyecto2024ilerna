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
- `Area`: Implementado con relaciones (parent/children, paths, user, medias)
- `Content`: Archivo base creado
- `Course`: Archivo base creado
- `Media`: Implementado para gestión de avatares y archivos
- `Message`: Archivo base creado
- `Path`: Archivo base creado

#### Controladores
- `UserController`: CRUD completo con búsqueda avanzada
- `UserSearchController`: API de búsqueda con sugerencias
- `AreaController`: Implementado con funcionalidades CRUD (admin y público)
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

#### Rutas y Permisos
- Rutas de autenticación configuradas
- Rutas para áreas (públicas y admin)
- Middleware para roles (admin, teacher, student)
- Prefijos de rutas por rol (/admin, /workarea, /classroom)
- Middleware `ensureRole` implementado
- Soporte para múltiples roles en rutas

#### Pendiente de Implementación
- Controladores para Path, Course y Content
- Vistas administrativas (excepto Users y Area)
- Vistas públicas para contenido
- Implementación completa de modelos Path, Course y Content
- Sistema de mensajes (Message model)
- Pruebas unitarias adicionales para nuevos modelos

### Próximos Pasos

1. Gestión de Áreas _(Prioridad Alta)_
   - Implementar CRUD completo usando componentes existentes
   - Reutilizar search-autocomplete para búsqueda
   - Adaptar avatar-upload para imágenes de área
   - Implementar jerarquía y relaciones
   - Validaciones específicas para áreas

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

## 📦 Estructura de Archivos Clave
```
proyecto2024ilerna/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── ...
│   │   │   ├── Api/
│   │   │   │   └── UserSearchController.php
│   │   │   ├── UserController.php
│   │   │   ├── ImageController.php
│   │   │   ├── ProfileController.php
│   │   │   └── AreaController.php
│   │   └── Middleware/
│   │       └── EnsureRole.php
│   ├── Models/
│   │   ├── Area.php
│   │   ├── User.php
│   │   └── Traits/
│   │       ├── HasAvatar.php
│   │       └── HasSlug.php
│   └── Services/
│       └── ImageService.php
├── config/
│   └── permission.php
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   └── ...
│   └── seeders/
│       └── RoleSeeder.php
├── resources/
│   ├── css/
│   │   └── app.css
│   └── views/
│       ├── admin/
│       │   └── users/
│       │       ├── index.blade.php
│       │       └── form.blade.php
│       ├── components/
│       │   ├── search-autocomplete.blade.php
│       │   └── media/
│       │       └── avatar-upload.blade.php
│       └── layouts/
│           ├── app.blade.php
│           └── navigation.blade.php
└── tests/
    └── Feature/
        ├── UserAvatarValidationTest.php
        ├── SlugGenerationTest.php
        └── ImageControllerTest.php
```

## 🎨 Paleta de Colores
- Principal: #fa5f30 (Naranja cálido)
  - Oscuro: #e54d20
  - Alternativo: #ff7346 (Logo)
- Secundario: #2a9d8f (Verde azulado)
- Éxito: #34d399 (Verde menta)
- Info: #60a5fa (Azul claro)
- Advertencia: #fbbf24 (Amarillo cálido)
- Peligro: #ef4444 (Rojo)
- Escala de grises personalizada (50-900)

## 🔧 Dependencias Principales
```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^11.31",
        "laravel/tinker": "^2.9",
        "livewire/livewire": "^3.5",
        "spatie/laravel-medialibrary": "^11.10",
        "spatie/laravel-permission": "^6.10"
    },
    "require-dev": {
        "barryvdh/laravel-debugbar": "^3.14",
        "fakerphp/faker": "^1.23",
        "laravel/breeze": "^2.2",
        "laravel/pail": "^1.1",
        "laravel/pint": "^1.13",
        "mockery/mockery": "^1.6",
        "phpunit/phpunit": "^11.0.1"
    }
}
```

## 📱 Características de la Interfaz
- Diseño responsive con Tailwind CSS
- Menú de navegación adaptativo
- Sistema de roles con iconos distintivos
- Formularios con validación del lado del cliente
- Gestión de avatares con previsualización
- Mensajes de error y éxito estilizados
- Componentes reutilizables con Alpine.js
- Búsqueda dinámica con autocompletado
- Indicadores de carga y estados

## 📚 Documentación de Referencia
Los siguientes archivos en `_research/` contienen información adicional relevante:
- `_1_intro.md`: Introducción y visión general
- `_2_tiempos-tareas-memoria.md`: Planificación detallada
- `_3_tabla-tiempos-tareas-MVP.md`: Estimaciones MVP
- `folio-volt-notes-Deep-Seek.md`: Notas de implementación
