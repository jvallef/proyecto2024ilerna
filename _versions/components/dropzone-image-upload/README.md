# Prueba de Subida de Imágenes con Dropzone.js

Este es un prototipo inicial de subida de imágenes usando Dropzone.js que se desarrolló antes de migrar a Spatie Media Library.

## Componentes

### Modelo
- `Image.php`: Modelo simple para almacenar información de imágenes subidas

### Controlador
- `UploadController.php`: Controlador para manejar la subida de imágenes con Dropzone.js

### Vista
- `upload/index.blade.php`: Vista con implementación de Dropzone.js para subida de múltiples imágenes

## Funcionalidad
- Subida de múltiples imágenes usando Dropzone.js
- Almacenamiento de imágenes en directorio public/images
- Almacenamiento de metadatos en tabla images
- Vista previa de imágenes subidas

## Por qué se reemplazó
Este código fue reemplazado por Spatie Media Library porque:
1. Spatie proporciona una solución más robusta y mantenida
2. Mejor manejo de colecciones de medios
3. Generación automática de conversiones de imágenes
4. Validación más completa
5. Mejor integración con Laravel
6. Soporte para múltiples discos de almacenamiento

## Fecha de Archivo
Noviembre 2023
