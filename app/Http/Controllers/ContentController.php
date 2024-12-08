<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Parsedown;

class ContentController extends Controller
{
    public function preview(Request $request)
    {
        Log::info('Preview request:', ['markdown' => $request->markdown]);
        
        if (empty($request->markdown)) {
            return response()->json([
                'error' => 'No hay contenido para mostrar',
                'preview' => ''
            ]);
        }

        $parsedown = new Parsedown();
        $html = $parsedown->text($request->markdown);
        
        Log::info('Preview HTML generated:', ['html' => $html]);

        return response()->json([
            'preview' => $html
        ]);
    }

    public function index()
    {
        $contents = Content::latest()->paginate(10);
        return view('admin.contents.index', compact('contents'));
    }

    public function create(Course $course)
    {
        $template = <<<MARKDOWN
# Título de la Lección

## Metadata
- Duración: 30 minutos
- Nivel: Básico
- Prerequisitos: Ninguno

## Descripción Breve
Escribe aquí una breve descripción de la lección...

## Objetivos de Aprendizaje
1. Primer objetivo
2. Segundo objetivo
3. Tercer objetivo

## Contenido Principal

### Sección 1
Tu contenido aquí...

![Imagen Descriptiva](placeholder) 
<!-- Para añadir una imagen, sube el archivo y reemplaza 'placeholder' con: !media[nombre-archivo] -->

### Sección 2
Más contenido aquí...

### Ejemplo Práctico
```python
def ejemplo():
    return "Esto es un ejemplo de código"
```

## Recursos Adicionales
- [Recurso 1](url1)
- [Recurso 2](url2)

## Archivos Adjuntos
- !file[presentacion.pdf]
- !file[ejercicios.zip]
<!-- Para añadir archivos, sube el archivo y usa la sintaxis !file[nombre-archivo] -->

## Evaluación
- Pregunta 1: ¿...?
- Pregunta 2: ¿...?

## Notas del Instructor
Notas privadas aquí...
MARKDOWN;

        return view('admin.contents.create', [
            'template' => $template,
            'course_id' => $course->id
        ]);
    }

    public function createTest(Request $request, $course = 1)
    {
        $template = <<<MARKDOWN
# Título de la Lección

## Metadata
- Duración: 30 minutos
- Nivel: Básico
- Prerequisitos: Ninguno

## Descripción Breve
Escribe aquí una breve descripción de la lección...

## Objetivos de Aprendizaje
1. Primer objetivo
2. Segundo objetivo
3. Tercer objetivo

## Contenido Principal

### Sección 1
Tu contenido aquí...

![Imagen Descriptiva](placeholder) 
<!-- Para añadir una imagen, sube el archivo y reemplaza 'placeholder' con: !media[nombre-archivo] -->

### Sección 2
Más contenido aquí...

### Ejemplo Práctico
```python
def ejemplo():
    return "Esto es un ejemplo de código"
```

## Recursos Adicionales
- [Recurso 1](url1)
- [Recurso 2](url2)

## Archivos Adjuntos
- !file[presentacion.pdf]
- !file[ejercicios.zip]
<!-- Para añadir archivos, sube el archivo y usa la sintaxis !file[nombre-archivo] -->

## Evaluación
- Pregunta 1: ¿...?
- Pregunta 2: ¿...?

## Notas del Instructor
Notas privadas aquí...
MARKDOWN;

        return view('admin.contents.create', [
            'template' => $template,
            'course_id' => $course
        ]);
    }

    public function store(Request $request)
    {
        try {
            Log::info('Iniciando store de content', [
                'request_data' => $request->all(),
                'has_course_id' => $request->has('course_id'),
                'course_id' => $request->input('course_id'),
                'route_name' => $request->route()->getName()
            ]);

            // Si es una petición AJAX para preview, solo devolvemos el HTML
            if ($request->ajax()) {
                $parsedown = new Parsedown();
                return response()->json([
                    'preview' => $parsedown->text($request->markdown)
                ]);
            }

            // Extraer título de la primera línea del markdown
            $title = trim(str_replace('#', '', explode("\n", $request->markdown)[0]));
            
            Log::info('Creando content', [
                'title' => $title,
                'markdown' => $request->markdown,
                'course_id' => $request->input('course_id')
            ]);

            // Crear el content
            $content = Content::create([
                'type' => 'lesson',
                'title' => $title,
                'content' => [
                    'markdown' => $request->markdown,
                    'type' => 'lesson',
                    'metadata' => [
                        'created_at' => now()->toDateTimeString(),
                        'author' => auth()->user()->name
                    ]
                ],
                'author_id' => auth()->id(),
                'status' => 'draft'
            ]);

            Log::info('Content creado', ['content_id' => $content->id]);

            // Vincular al curso si se proporciona course_id
            if ($request->filled('course_id')) {
                $courseId = $request->input('course_id');
                $maxSort = $content->courses()->where('course_id', $courseId)->max('sort') ?? 0;
                
                Log::info('Vinculando content al curso', [
                    'content_id' => $content->id,
                    'course_id' => $courseId,
                    'sort' => $maxSort + 1
                ]);

                $content->courses()->attach($courseId, [
                    'sort' => $maxSort + 1
                ]);
                
                Log::info('Content vinculado al curso exitosamente');
            } else {
                Log::info('No se proporcionó course_id, el content no será vinculado a ningún curso');
            }

            // Procesar archivos si existen
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $content->addMedia($file)
                            ->toMediaCollection('content-files');
                }
                Log::info('Archivos procesados');
            }

            if ($request->filled('course_id')) {
                return redirect()->route('admin.courses.show', $request->input('course_id'))
                               ->with('success', 'Contenido creado y añadido al curso exitosamente');
            }

            return redirect()->route('admin.contents.index')
                           ->with('success', 'Contenido creado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error creating content: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error al crear el contenido: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function show(Course $course, Content $content)
    {
        $parsedown = new Parsedown();
        
        // Obtener el markdown del contenido
        $markdown = $content->content['markdown'] ?? '';
        
        // Obtener todos los archivos adjuntos
        $mediaItems = $content->getMedia('content-files');
        $mediaByName = collect();
        
        // Crear un índice que incluya tanto el nombre con extensión como sin ella
        foreach ($mediaItems as $media) {
            $mediaByName->put($media->file_name, $media);
            $mediaByName->put($media->name, $media);
            $nameWithoutExt = pathinfo($media->file_name, PATHINFO_FILENAME);
            $mediaByName->put($nameWithoutExt, $media);
        }
        
        // Procesar imágenes y enlaces en el markdown
        $processedMarkdown = $this->processMarkdownWithMedia($markdown, $mediaByName);
        
        // Convertir a HTML
        $html = $parsedown->text($processedMarkdown);
        
        return view('admin.contents.show', [
            'content' => $content,
            'html' => $html,
            'course' => $course
        ]);
    }

    /**
     * Procesa el markdown reemplazando las referencias a archivos con sus URLs
     */
    private function processMarkdownWithMedia($markdown, $mediaByName)
    {
        // Procesar imágenes: ![alt](filename)
        $markdown = preg_replace_callback(
            '/!\[(.*?)\]\((.+?)\)/',
            function($matches) use ($mediaByName) {
                $alt = $matches[1];
                $filename = basename($matches[2]);
                
                if ($mediaByName->has($filename)) {
                    $media = $mediaByName->get($filename);
                    return sprintf(
                        '![%s](%s)',
                        $alt,
                        $media->getUrl()
                    );
                }
                
                return $matches[0];
            },
            $markdown
        );
        
        // Procesar enlaces normales: [text](filename)
        $markdown = preg_replace_callback(
            '/\[(.*?)\]\((.+?)\)/',
            function($matches) use ($mediaByName) {
                $text = $matches[1];
                $filename = basename($matches[2]);
                
                if ($mediaByName->has($filename)) {
                    $media = $mediaByName->get($filename);
                    return sprintf(
                        '[%s](%s)',
                        $text,
                        $media->getUrl()
                    );
                }
                
                return $matches[0];
            },
            $markdown
        );

        // Procesar !file[filename]: formato especial para archivos
        $markdown = preg_replace_callback(
            '/!file\[(.*?)\]/',
            function($matches) use ($mediaByName) {
                $filename = $matches[1];
                
                if ($mediaByName->has($filename)) {
                    $media = $mediaByName->get($filename);
                    $icon = $this->getFileIcon($media->mime_type);
                    return sprintf(
                        '[%s %s](%s)',
                        $icon,
                        $filename,
                        $media->getUrl()
                    );
                }
                
                return $matches[0];
            },
            $markdown
        );
        
        return $markdown;
    }

    private function getFileIcon($mimeType)
    {
        if (str_contains($mimeType, 'pdf')) {
            return '📄';
        } elseif (str_contains($mimeType, 'word')) {
            return '📝';
        } elseif (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheet')) {
            return '📊';
        } elseif (str_contains($mimeType, 'powerpoint') || str_contains($mimeType, 'presentation')) {
            return '📊';
        } elseif (str_contains($mimeType, 'zip') || str_contains($mimeType, 'rar') || str_contains($mimeType, 'tar')) {
            return '📦';
        } elseif (str_contains($mimeType, 'video')) {
            return '🎥';
        } elseif (str_contains($mimeType, 'audio')) {
            return '🎵';
        } else {
            return '📄';
        }
    }
    
    /**
     * Muestra el formulario de edición de un contenido
     */
    public function edit(Course $course, Content $content)
    {
        return view('admin.contents.edit', [
            'content' => $content,
            'course_id' => $course->id,
            'markdown' => $content->content['markdown']
        ]);
    }

    /**
     * Actualiza un contenido existente
     */
    public function update(Request $request, Course $course, Content $content)
    {
        try {
            // Extraer título de la primera línea del markdown
            $title = trim(str_replace('#', '', explode("\n", $request->markdown)[0]));
            
            // Actualizar el contenido
            $content->update([
                'title' => $title,
                'content' => [
                    'markdown' => $request->markdown,
                    'type' => 'lesson',
                    'metadata' => [
                        'updated_at' => now()->toDateTimeString(),
                        'editor' => auth()->user()->name
                    ]
                ]
            ]);

            // Procesar archivos si existen
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $content->addMedia($file)
                            ->toMediaCollection('content-files');
                }
            }

            return redirect()->route('admin.courses.show', $course)
                           ->with('success', 'Contenido actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error updating content: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error al actualizar el contenido: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Elimina un contenido del curso
     */
    public function destroy(Course $course, Content $content)
    {
        try {
            // Primero desvinculamos el contenido del curso
            $content->courses()->detach($course->id);
            
            // Si el contenido no está vinculado a ningún otro curso, lo eliminamos completamente
            if ($content->courses()->count() === 0) {
                // Eliminar todos los archivos multimedia asociados
                $content->clearMediaCollection('content-files');
                
                // Eliminar el contenido
                $content->delete();
                
                return redirect()->route('admin.courses.show', $course)
                               ->with('success', 'Contenido eliminado completamente');
            }
            
            return redirect()->route('admin.courses.show', $course)
                           ->with('success', 'Contenido desvinculado del curso');
                           
        } catch (\Exception $e) {
            Log::error('Error eliminando contenido: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error al eliminar el contenido: ' . $e->getMessage());
        }
    }

    private function parseMarkdownToJson($markdown)
    {
        return [
            'markdown' => $markdown,
            'type' => 'lesson',
            'metadata' => [
                'duration' => '30 minutes',
                'level' => 'basic'
            ],
            'content' => $markdown
        ];
    }
}
