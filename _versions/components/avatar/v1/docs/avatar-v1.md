# Avatar Upload Component - Version 1

## Descripción
Componente para la subida de avatares de usuario utilizando Spatie Media Library. Esta implementación incluye validación tanto en el cliente como en el servidor, y utiliza variables de entorno para la configuración.

## Archivos
- Vista: `resources/views/tests/media/versions/avatar-v1.blade.php`
- Controlador: `app/Http/Controllers/TestAvatarControllerV1.php`

## Características
1. **Validación en el Cliente (JavaScript)**
   - Validación de tamaño de archivo
   - Validación de tipo de archivo
   - Feedback inmediato al usuario
   - Previene el envío de archivos inválidos

2. **Validación en el Servidor**
   - Validación de campos de usuario (nombre, email)
   - Validación de imagen (tamaño, tipo)
   - Uso de las reglas de validación de Laravel

3. **Configuración mediante Variables de Entorno**
   - `AVATAR_MAX_FILE_SIZE`: Tamaño máximo del archivo en KB
   - `AVATAR_ALLOWED_TYPES`: Tipos de archivo permitidos
   - `AVATAR_MAX_DIMENSIONS`: Dimensiones máximas de la imagen

4. **Interfaz de Usuario**
   - Formulario con campos de usuario
   - Selector de archivo con estilos personalizados
   - Mensajes de error claros y específicos
   - Información sobre restricciones de archivos

5. **Integración con Spatie Media Library**
   - Uso de la colección 'avatar'
   - Almacenamiento automático de archivos
   - Gestión de medios a través de la librería

## Uso
1. Acceder a la ruta `/tests/media/avatar`
2. Rellenar el formulario con:
   - Nombre de usuario
   - Email
   - Seleccionar un archivo de imagen para el avatar
3. El sistema validará:
   - Que el archivo sea una imagen válida
   - Que no exceda el tamaño máximo
   - Que sea de un tipo permitido

## Limitaciones y Consideraciones
- No incluye previsualización de imagen
- No permite recorte de imagen
- No maneja múltiples avatares
- Utiliza una contraseña por defecto para testing

## Mejoras Potenciales
1. Añadir previsualización de imagen
2. Implementar recorte de imagen
3. Añadir validación de dimensiones mínimas/máximas
4. Mejorar el manejo de errores
5. Añadir compresión de imágenes

## Dependencias
- Laravel Framework
- Spatie Media Library
- Tailwind CSS para estilos
