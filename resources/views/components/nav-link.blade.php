@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-4 border-b-2 border-white text-base leading-5 text-white focus:outline-none focus:border-white transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-4 border-b-2 border-transparent text-base leading-5 text-white hover:text-gray-200 hover:border-gray-200 focus:outline-none focus:text-gray-200 focus:border-gray-200 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
