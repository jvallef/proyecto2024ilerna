<?php

namespace App\Http\Controllers;

use App\Models\Content;
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

    public function create()
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

        return view('admin.contents.create', compact('template'));
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

            return redirect()->route('admin.contents.index')
                           ->with('success', 'Contenido creado exitosamente' . 
                                ($request->filled('course_id') ? ' y vinculado al curso' : ''));
        } catch (\Exception $e) {
            Log::error('Error creating content: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error al crear el contenido: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function show(Content $content)
    {
        $parsedown = new Parsedown();
        $markdown = $content->content['markdown'];
        
        // Obtener todos los archivos adjuntos
        $mediaItems = $content->getMedia('content-files');
        $mediaByName = collect();
        
        // Crear un índice que incluya tanto el nombre con extensión como sin ella
        foreach ($mediaItems as $media) {
            $mediaByName->put($media->file_name, $media);
            $mediaByName->put($media->name, $media);
            // También añadir sin la extensión por si acaso
            $nameWithoutExt = pathinfo($media->file_name, PATHINFO_FILENAME);
            $mediaByName->put($nameWithoutExt, $media);
        }
        
        // Procesar imágenes: ![alt](filename)
        $markdown = preg_replace_callback(
            '/!\[(.*?)\]\((.+?)\)/',
            function($matches) use ($mediaByName) {
                $alt = $matches[1];
                $filename = basename($matches[2]);
                
                if ($mediaByName->has($filename)) {
                    $media = $mediaByName->get($filename);
                    return sprintf(
                        '<img src="%s" alt="%s" class="max-w-full h-auto rounded-lg shadow-md">',
                        $media->getUrl(),
                        $alt
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
                    $icon = $this->getFileIcon($media->mime_type);
                    return sprintf(
                        '<a href="%s" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150" target="_blank">%s%s</a>',
                        $media->getUrl(),
                        $icon,
                        $text
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
                    $displayName = $media->file_name;
                    return sprintf(
                        '<a href="%s" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150" target="_blank">%s%s</a>',
                        $media->getUrl(),
                        $icon,
                        $displayName
                    );
                }
                
                return $matches[0];
            },
            $markdown
        );
        
        $html = $parsedown->text($markdown);
        
        return view('admin.contents.show', [
            'content' => $content,
            'html' => $html
        ]);
    }
    
    /**
     * Muestra el formulario de edición de un contenido
     */
    public function edit($content)
    {
        return view('admin.contents.edit', ['message' => 'Esto es la edición de un contenido']);
    }

    private function getFileIcon($mimeType)
    {
        $icon = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
        
        if (str_starts_with($mimeType, 'image/')) {
            $icon .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />';
        } elseif (str_starts_with($mimeType, 'video/')) {
            $icon .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />';
        } elseif ($mimeType === 'application/pdf') {
            $icon .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />';
        } else {
            $icon .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />';
        }
        
        $icon .= '</svg>';
        return $icon;
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
