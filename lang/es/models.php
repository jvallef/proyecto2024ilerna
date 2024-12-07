<?php

return [
    'common' => [
        'required' => 'El :attribute es obligatorio',
        'max' => 'El :attribute no puede tener más de :max caracteres',
        'unique' => 'Este :attribute ya está en uso',
        'exists' => 'El :attribute seleccionado no existe',
    ],
    
    'course' => [
        'attributes' => [
            'title' => 'título',
            'description' => 'descripción',
            'age_group' => 'grupo de edad',
            'status' => 'estado',
            'image' => 'imagen de portada',
            'banner' => 'imagen de cabecera',
            'files' => 'archivos',
            'paths' => 'rutas',
            'contents' => 'contenidos',
        ],
        'age_group' => [
            'invalid' => 'El grupo de edad seleccionado no es válido',
        ],
        'status' => [
            'invalid' => 'El estado seleccionado no es válido',
        ],
        'media' => [
            'image_type' => 'La imagen debe ser de tipo: :values',
            'image_size' => 'La imagen no puede ser mayor a :max kilobytes',
            'banner_dimensions' => 'La imagen debe tener exactamente :width x :height píxeles',
            'file_size' => 'El archivo no puede ser mayor a :max kilobytes',
        ],
    ],
    
    'path' => [
        'attributes' => [
            'name' => 'nombre',
            'description' => 'descripción',
            'parent_id' => 'ruta padre',
            'area_id' => 'área',
            'image' => 'imagen de portada',
        ],
    ],
    
    'area' => [
        'attributes' => [
            'name' => 'nombre',
            'description' => 'descripción',
        ],
    ],
];
