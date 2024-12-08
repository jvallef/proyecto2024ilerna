<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class PythonContentSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('title', 'Fundamentos de Python')->first();
        $teacher = User::role('teacher')->first();

        if (!$course || !$teacher) {
            throw new \Exception("No se encontró el curso de Python o un profesor. Asegúrate de que los seeders previos se ejecutaron correctamente.");
        }

        // Contenido 1: Estructura del curso
        $content1 = Content::create([
            'title' => 'Estructura del Curso de Python',
            'type' => 'theme',
            'status' => 'published',
            'author_id' => $teacher->id,
            'content' => [
                'markdown' => "# Estructura del Curso de Python

Este curso está diseñado para llevarte desde los conceptos básicos hasta un nivel intermedio en Python.

## Temas del curso

1. **Variables y Tipos de Datos**
   - Asignación de variables
   - Tipos de datos integrados (int, float, str, bool)
   - Listas, tuplas y diccionarios

2. **Operadores**
   - Operadores aritméticos
   - Operadores de comparación
   - Operadores lógicos

3. **Control de Flujo**
   - Condicionales (if, elif, else)
   - Bucles (for, while)
   - Control de bucles (break, continue)

4. **Funciones**
   - Definición de funciones
   - Parámetros y argumentos
   - Return y scope

5. **Estructuras de Datos**
   - Listas avanzadas
   - Diccionarios avanzados
   - Sets y tuplas

6. **Programación Orientada a Objetos**
   - Clases y objetos
   - Herencia
   - Encapsulamiento

## Material de Referencia

Para complementar tu aprendizaje, te proporcionamos el siguiente material:

!file[python-tutorial.pdf]

Este tutorial contiene ejemplos detallados y ejercicios prácticos para cada tema."
            ]
        ]);

        // Contenido 2: Variables y Tipos de Datos (primer tema)
        $content2 = Content::create([
            'title' => 'Variables y Tipos de Datos en Python',
            'type' => 'lesson',
            'status' => 'published',
            'author_id' => $teacher->id,
            'content' => [
                'markdown' => "# Variables y Tipos de Datos en Python

En esta lección aprenderemos los fundamentos de las variables y tipos de datos en Python.

## Asignación de Variables

En Python, las variables se crean y asignan usando el operador `=`:

```python
nombre = 'Juan'
edad = 25
altura = 1.75
es_estudiante = True
```

## Tipos de Datos Básicos

Python tiene varios tipos de datos integrados:

### Números
- Enteros (int): `42`, `-17`, `1000`
- Flotantes (float): `3.14`, `-0.001`, `2.0`

### Texto
- Cadenas (str): `'Hola'`, `\"Python\"`, `'''Texto largo'''`

### Booleanos
- `True` o `False`

### Colecciones
1. Listas: `[1, 2, 3]`, `['a', 'b', 'c']`
2. Tuplas: `(1, 2, 3)`, `('x', 'y', 'z')`
3. Diccionarios: `{'nombre': 'Ana', 'edad': 30}`

## Ejercicios Prácticos

Prueba estos ejemplos en tu editor:

```python
# Crear diferentes tipos de variables
nombre = 'Python'
version = 3.9
es_facil = True

# Crear una lista
lenguajes = ['Python', 'Java', 'JavaScript']

# Crear un diccionario
info_curso = {
    'nombre': 'Python Básico',
    'duración': '4 semanas',
    'nivel': 'Principiante'
}
```

## Material de Referencia

Para más detalles y ejemplos, consulta:

!file[python-tutorial.pdf]

En la siguiente lección, exploraremos los operadores en Python."
            ]
        ]);

        // Copiar el archivo PDF a storage y adjuntarlo a ambos contenidos
        $pdfPath = public_path('python-tutorial.pdf');
        if (file_exists($pdfPath)) {
            $content1->addMedia($pdfPath)
                    ->preservingOriginal()
                    ->toMediaCollection('content-files');
            
            $content2->addMedia($pdfPath)
                    ->preservingOriginal()
                    ->toMediaCollection('content-files');
        }

        // Asociar contenidos al curso
        $course->contents()->attach([
            $content1->id => ['sort' => 1],
            $content2->id => ['sort' => 2]
        ]);
    }
}
