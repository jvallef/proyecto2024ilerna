ahora de acuerdo con analisis-paths-v1.md, crearás el prompt para el paso que te he indicado.

Utiliza este patrón para crear el prompt, e incluye todos los apartados que se indican. El prompt que crearás es el que te servirá luego para realizar la tarea, paso a paso. Recuerda añadir siempre referencias para verificar lo que sea necesario de Areas u otros elemntos relaciondos con Paths o Areas.

El Prompt creado lo añadirás al archivo tareas-paths-v1.md

Esta es la plantilla que usarás para crear el prompt:

### Pasos Previos Genéricos
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

Para mantener la consistencia y evitar errores, cada tarea debe seguir este formato:

### Contexto Específico

CONTEXTO INMEDIATO:
- Archivo/s a modificar: [lista]
- Funcionalidad actual: [descripción]
- Campos/métodos/relaciones: [lista]
- Dependencias: [traits, interfaces, observers]
- Validaciones existentes: [reglas]
- Eventos actuales: [lista]

### Restricciones Explícitas

RESTRICCIONES:
- NO crear nuevas funcionalidades
- NO modificar: [lista]
- MANTENER: [lista]
- USAR SOLO: [lista]
- RESPETAR estructura de: [elementos]
- SEGUIR patrones de: [elementos]

### Referencia Concreta

REFERENCIA:
- Implementación base: [archivo/funcionalidad]
- Documentación: [archivos]
- Tests relacionados: [archivos]
- Commits relevantes: [lista]

### Objetivo Simple

TAREA:
- Acción concreta: [descripción]
- Resultado esperado: [descripción]
- Dependencias previas: [lista]

### Verificación

VERIFICACIÓN:
- Comprobar: [lista]
- Validar: [lista]
- Tests necesarios: [lista]
- Integridad referencial: [checks]