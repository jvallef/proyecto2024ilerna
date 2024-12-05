@props(['status', 'type' => 'path'])

@php
    $statusClasses = [
        'draft' => 'bg-yellow-100 text-yellow-800',
        'published' => 'bg-green-100 text-green-800',
        'suspended' => 'bg-red-100 text-red-800',
    ];

    $statusLabels = [
        'draft' => 'Borrador',
        'published' => 'Publicado',
        'suspended' => 'Suspendido',
    ];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$status] }}">
    {{ $statusLabels[$status] }}
</span>
