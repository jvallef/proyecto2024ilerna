# Documentación del Componente Single Image Upload - v1

## Estado Actual

La versión actual del componente de carga de imágenes individuales está funcionando correctamente con los siguientes archivos:

### Archivos Principales

1. `resources/views/tests/media/single-image.blade.php` (v1)
   - Vista principal que implementa la interfaz de carga
   - Layout de dos columnas: dropzone y panel de información
   - Integración completa con Tailwind CSS
   - Muestra información de restricciones (tipos permitidos, tamaño máximo, dimensiones)

2. `public/js/components/media/MediaUploader.js` (v1)
   - Clase base para la gestión de subidas
   - Configuración de Dropzone.js
   - Manejo de mensajes y errores
   - Formateo de tamaños de archivo
   - NO MODIFICAR este archivo

3. `public/js/components/media/SingleImageUploader.js` (v1)
   - Extiende MediaUploader
   - Gestiona el panel de información de la imagen
   - Validación en tiempo real de:
     * Tipo de archivo
     * Tamaño
     * Dimensiones
   - NO MODIFICAR este archivo

### Configuración

La configuración actual se maneja a través de variables de entorno:

```env
AVATAR_MAX_FILE_SIZE=10240
AVATAR_MAX_DIMENSIONS=10000
AVATAR_ALLOWED_TYPES="jpg,jpeg,png,webp"
```

### Funcionalidad

El componente proporciona:
1. Zona de arrastrar y soltar para subida de archivos
2. Panel de información que muestra en tiempo real:
   - Nombre del archivo con validación de tipo
   - Dimensiones con validación de límites
   - Tamaño con validación de límites
3. Botón de subida que procesa el archivo
4. Mensajes de error/éxito

### Integración

El componente requiere:
1. Dropzone.js v5
2. Tailwind CSS
3. Laravel Blade
4. CSRF token configurado
5. Rutas media.store configuradas

### Próximos Pasos

1. Integrar esta funcionalidad en `single-image-form.blade.php`
2. Mantener la funcionalidad actual sin modificar los archivos JavaScript
3. Adaptar solo el diseño y la estructura HTML según sea necesario

## Notas Importantes

- Esta versión está probada y funciona correctamente
- NO se deben modificar los archivos JavaScript
- Cualquier cambio debe hacerse a nivel de vista (blade)
- Los estilos pueden ajustarse según necesidades específicas
- La configuración de tamaños y tipos permitidos debe mantenerse en .env
