# Tests para el Módulo de Áreas

Para crear cualquier test revisa todos los archivos implicados. No crees un test si no haces ese ejercicio.

## Estructura de Tests

### 1. AreaControllerTest.php

Este archivo probará todas las acciones del controlador de áreas.

```php
/**
 * Tests principales:
 * 
 * 1. index()
 *    - Debe mostrar lista de áreas paginada
 *    - Debe mostrar solo áreas no eliminadas
 *    - Debe respetar permisos de usuario
 * 
 * 2. create()
 *    - Debe mostrar formulario de creación
 *    - Solo accesible para usuarios autorizados
 * 
 * 3. store()
 *    - Debe crear área con datos válidos
 *    - Debe manejar subida de imagen de portada
 *    - Debe generar slug único
 *    - Debe validar datos requeridos
 * 
 * 4. edit()
 *    - Debe mostrar formulario con datos existentes
 *    - Solo accesible para usuarios autorizados
 * 
 * 5. update()
 *    - Debe actualizar área con datos válidos
 *    - Debe mantener o actualizar imagen de portada
 *    - Debe validar datos
 * 
 * 6. destroy()
 *    - Debe realizar soft delete
 *    - Debe mantener integridad referencial
 * 
 * 7. restore()
 *    - Debe restaurar área eliminada
 *    - Solo accesible para usuarios autorizados
 * 
 * 8. forceDelete()
 *    - Debe eliminar permanentemente
 *    - Solo accesible para administradores
 */
```

### 2. AreaHierarchyTest.php

Tests específicos para la funcionalidad jerárquica de áreas.

```php
/**
 * Tests de jerarquía:
 * 
 * 1. Creación de jerarquías
 *    - Crear área padre
 *    - Crear área hija
 *    - Validar relaciones parent/children
 * 
 * 2. Navegación jerárquica
 *    - Obtener ruta completa (breadcrumb)
 *    - Obtener todas las áreas hijas
 *    - Obtener árbol jerárquico completo
 * 
 * 3. Restricciones jerárquicas
 *    - Evitar ciclos en la jerarquía
 *    - Validar profundidad máxima
 *    - Manejar eliminación de padre
 * 
 * 4. Ordenamiento
 *    - Ordenar áreas del mismo nivel
 *    - Mantener orden al mover áreas
 */
```

### 3. AreaValidationTest.php

Tests específicos para validación de datos.

```php
/**
 * Tests de validación:
 * 
 * 1. Validación de campos requeridos
 *    - name
 *    - slug (generado automáticamente)
 *    - user_id
 * 
 * 2. Validación de campos opcionales
 *    - description
 *    - parent_id
 *    - featured
 *    - status
 *    - meta
 * 
 * 3. Validación de imagen de portada
 *    - Tipos de archivo permitidos
 *    - Tamaño máximo
 *    - Dimensiones permitidas
 * 
 * 4. Validación de unicidad
 *    - Slug único
 *    - Nombre único por nivel
 * 
 * 5. Validación de estados
 *    - Transiciones de estado válidas
 *    - Permisos por estado
 */
```

### 4. AreaSearchTest.php

Tests para funcionalidad de búsqueda y filtrado.

```php
/**
 * Tests de búsqueda:
 * 
 * 1. Búsqueda básica
 *    - Por nombre
 *    - Por descripción
 *    - Por slug
 * 
 * 2. Filtros
 *    - Por estado
 *    - Por featured
 *    - Por usuario creador
 * 
 * 3. Ordenamiento
 *    - Por nombre
 *    - Por fecha
 *    - Por sort_order
 * 
 * 4. Paginación
 *    - Límite por página
 *    - Navegación entre páginas
 * 
 * 5. Búsqueda jerárquica
 *    - Dentro de un área padre
 *    - En todos los niveles
 */
```

## Notas sobre Implementación de Tests

#### 1. publicIndex_should_show_published_areas

Dificultades y lecciones aprendidas:

1. **Datos Existentes**: 
   - Error inicial al intentar crear datos de prueba manualmente cuando ya existían datos en los seeders
   - Solución: Aprovechar los datos existentes en lugar de crear nuevos, lo que simplifica el test y lo hace más realista

2. **Paginación**:
   - Error al asumir un valor hardcodeado de 12 items por página
   - Solución: Usar el valor real del entorno `env('PAGINATION_PER_PAGE', 10)`

3. **Roles y Permisos**:
   - No se consideró inicialmente que el acceso a ciertas rutas requiere un usuario con rol específico
   - Importante: Asegurarse de que el usuario tiene los permisos necesarios antes de hacer las peticiones

4. **Mejores Prácticas**:
   - Evitar crear datos innecesarios cuando ya existen seeders
   - Usar los valores de configuración del entorno en lugar de hardcodear valores
   - Verificar permisos y roles necesarios antes de las pruebas

## Consideraciones Generales

1. **Base de Datos**
   - La base de datos tiene datos de prueba
   - Puedes usar los datos existentes, pero no puedes modificarlos, ni eliminarlos, o bien crear los tuyos propios
   - Usar factories para datos de prueba
   - Limpiar datos entre tests

2. **Autenticación y Autorización**
   - Probar con diferentes roles de usuario
   - Verificar permisos específicos
   - Simular usuarios autenticados

3. **Media Library**
   - Usar disco fake para archivos
   - Limpiar archivos después de tests
   - Validar conversiones de imagen

4. **Eventos y Jobs**
   - Fake eventos relacionados
   - Verificar jobs disparados
   - Probar listeners

5. **Caché**
   - Usar driver array para caché
   - Limpiar caché entre tests
   - Verificar invalidación de caché
