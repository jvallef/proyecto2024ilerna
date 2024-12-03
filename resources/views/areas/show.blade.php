<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h2 class="text-3xl font-semibold mb-2">{{ $area->name }}</h2>
                        @if($area->parent)
                            <p class="text-gray-600">
                                {{ __('Área padre') }}: 
                                <a href="{{ route('areas.show', $area->parent->slug) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    {{ $area->parent->name }}
                                </a>
                            </p>
                        @endif
                    </div>

                    <div class="prose max-w-none mb-8">
                        {{ $area->description }}
                    </div>

                    @if($area->children->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-2xl font-semibold mb-4">{{ __('Sub Áreas') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($area->children as $child)
                                    <div class="bg-white rounded-lg shadow-md p-6">
                                        <h4 class="text-xl font-semibold mb-2">{{ $child->name }}</h4>
                                        <p class="text-gray-600 mb-4">{{ Str::limit($child->description, 100) }}</p>
                                        <a href="{{ route('areas.show', $child->slug) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            {{ __('Ver detalles') }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($area->paths->count() > 0)
                        <div>
                            <h3 class="text-2xl font-semibold mb-4">{{ __('Rutas de Aprendizaje') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($area->paths as $path)
                                    <div class="bg-white rounded-lg shadow-md p-6">
                                        <h4 class="text-xl font-semibold mb-2">{{ $path->name }}</h4>
                                        <p class="text-gray-600 mb-4">{{ $path->description }}</p>
                                        <div class="flex justify-between items-center">
                                            <a href="{{ route('areas.progress', $area->slug) }}" 
                                               class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                                {{ __('Ver progreso') }} →
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
