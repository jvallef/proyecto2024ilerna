# Single Image Upload Component - Version 1

Esta es la versión inicial del componente de carga de imágenes individuales.

## Características
- Implementación básica de Dropzone.js
- Validación de archivos en cliente y servidor
- Mensajes de error/éxito con duración de 5 segundos
- Estilo basado en Breeze/Tailwind

## Configuración
- Tamaño máximo: 2048 KB (2 MB)
- Dimensiones máximas: 10000x10000 pixels
- Tipos permitidos: jpg, jpeg, png, webp

## Limitaciones conocidas
- No muestra información previa de la imagen
- El procesamiento del thumbnail puede tardar varios segundos
- Los mensajes de error/éxito desaparecen muy rápido

## Fecha
- Versión creada: {{ date('Y-m-d') }}
