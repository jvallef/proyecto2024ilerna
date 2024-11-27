# Avatar Upload Component - Version 2

## Cambios respecto a V1
- Creación de un componente Blade reutilizable (`avatar-upload`)
- Encapsulación de toda la lógica JavaScript en el componente
- Mejora en la organización del código
- Implementación de traducciones para mensajes de validación

## Estructura

```
v2/
├── components/
│   └── avatar-upload.blade.php    # Nuevo componente reutilizable
├── controllers/
│   └── TestAvatarController.php   # Controlador de prueba
├── views/
│   └── avatar.blade.php           # Vista de prueba simplificada
└── docs/
    └── README.md                  # Esta documentación
```

## Componente Reutilizable
El nuevo componente `avatar-upload` encapsula:
- Input de tipo file con estilos
- Validación en el cliente (JavaScript)
- Mensajes de error y validación
- Configuración mediante variables de entorno

### Props disponibles
- `name`: Nombre del campo (default: 'avatar')
- `label`: Etiqueta del campo (default: 'Avatar')
- `required`: Si es requerido (default: true)
- `value`: Valor actual (default: null)
- `error`: Mensaje de error (default: null)

## Variables de Entorno
```env
AVATAR_MAX_FILE_SIZE=4096
AVATAR_MAX_DIMENSIONS=2000
AVATAR_ALLOWED_TYPES="jpg,jpeg,png,webp"
```

## Uso del Componente
```blade
<x-media.avatar-upload 
    name="profile_photo"
    label="Foto de Perfil"
    :required="true"
/>
```

## Validaciones
### Cliente
- Tamaño máximo del archivo
- Tipos de archivo permitidos
- Feedback inmediato al usuario

### Servidor
- Validación de campos de usuario (nombre, email)
- Validación de imagen (tamaño, tipo)
- Mensajes de error traducidos

## Traducciones
Se han añadido traducciones en español para los mensajes de validación, incluyendo:
- Mensajes de error de validación
- Mensaje para email duplicado
- Mensajes de tamaño y tipo de archivo

## Mejoras respecto a V1
1. **Reutilización**: El componente puede usarse en cualquier formulario
2. **Mantenibilidad**: Código más organizado y centralizado
3. **Consistencia**: UI y validaciones uniformes
4. **Traducciones**: Mejor experiencia para usuarios hispanohablantes
5. **Configuración**: Más flexible mediante variables de entorno
