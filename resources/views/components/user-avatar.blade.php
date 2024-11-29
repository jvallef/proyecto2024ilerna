@props(['user', 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'h-8 w-8',
        'md' => 'h-10 w-10',
        'lg' => 'h-12 w-12',
        'xl' => 'h-16 w-16'
    ];
    
    $textSizes = [
        'sm' => 'text-xs',
        'md' => 'text-sm',
        'lg' => 'text-base',
        'xl' => 'text-lg'
    ];
    
    $containerSize = $sizes[$size] ?? $sizes['md'];
    $textSize = $textSizes[$size] ?? $textSizes['md'];
    
    $avatarUrl = $user->getFirstMediaUrl('avatar');
    
    // Obtener iniciales: dos primeras letras de cada palabra o las dos primeras si es una palabra
    $nameParts = explode(' ', trim($user->name));
    $initials = '';
    
    if (count($nameParts) > 1) {
        // Usar mb_substr para manejar correctamente caracteres UTF-8
        $initials = mb_strtoupper(
            mb_substr($nameParts[0], 0, 1, 'UTF-8') . 
            mb_substr($nameParts[1], 0, 1, 'UTF-8'), 
            'UTF-8'
        );
    } else {
        $initials = mb_strtoupper(mb_substr($user->name, 0, 2, 'UTF-8'), 'UTF-8');
    }
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center']) }}>
    <div class="flex-shrink-0 {{ $containerSize }}">
        @if($avatarUrl)
            <img class="{{ $containerSize }} rounded-full object-cover border border-gray-200" 
                 src="{{ $avatarUrl }}" 
                 alt="{{ $user->name }}">
        @else
            <div class="{{ $containerSize }} rounded-full bg-green-100 border border-green-200 flex items-center justify-center">
                <span class="{{ $textSize }} font-medium text-green-700">
                    {{ $initials }}
                </span>
            </div>
        @endif
    </div>
    <div class="ml-4">
        <div class="{{ $textSize }} font-medium text-gray-900">
            {{ $user->name }}
        </div>
        <div class="{{ $textSize }} text-gray-500">
            {{ $user->email }}
        </div>
    </div>
</div>
