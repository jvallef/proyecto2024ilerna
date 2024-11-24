@props(['mode' => 'light'])

@if(request()->routeIs('login') || request()->routeIs('register'))
    <img src="{{ asset('img/logo-alt.svg') }}" alt="Logo" {{ $attributes->merge(['class' => 'h-12 w-auto']) }}>
@else
    <img src="{{ asset('img/logo.svg') }}" alt="Logo" {{ $attributes->merge(['class' => 'h-12 w-auto']) }}>
@endif
