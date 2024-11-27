# Sistema de Versionado de Componentes

## Estructura

_versions/
├── components/        # Componentes de UI reutilizables
│   ├── avatar/       # Sistema de gestión de avatares
│   └── single-image/ # Sistema de gestión de imágenes individuales
├── controllers/      # Versiones de controladores específicos
├── models/          # Versiones de modelos
└── services/        # Versiones de servicios o lógica de negocio

## Convenciones de Versionado

- Cada componente mantiene sus propias versiones (v1, v2, etc.)
- Cada versión contiene todos los archivos necesarios para su funcionamiento
- La documentación es obligatoria en cada versión