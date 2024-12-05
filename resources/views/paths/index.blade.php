<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Rutas') }}
            </h2>
            @can('manage paths')
            <a href="{{ route('admin.paths.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                {{ __('Nueva Ruta') }}
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif

            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1 max-w-lg">
                        <x-search-autocomplete 
                            :route="route('paths.index')"
                            :search-url="route('api.paths.search.public')"
                            placeholder="Buscar por nombre o descripción..."
                            :min-chars="2" />
                    </div>
                </div>
            </div>

            @if($featuredPaths->count() > 0)
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">Rutas Destacadas</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($featuredPaths as $path)
                            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                                <div class="flex items-center space-x-4">
                                    @if($path->getFirstMediaUrl('cover'))
                                        <img src="{{ $path->getFirstMediaUrl('cover') }}" 
                                             alt="{{ $path->name }}" 
                                             class="h-16 w-16 object-cover rounded-lg">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-gray-900 truncate">
                                            {{ $path->name }}
                                        </h3>
                                        @if($path->parent)
                                            <p class="text-sm text-gray-500">
                                                {{ $path->parent->name }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($path->description)
                                    <p class="mt-4 text-gray-600 line-clamp-3">
                                        {{ $path->description }}
                                    </p>
                                @endif

                                <div class="mt-4 flex items-center justify-between">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $path->status === 'published' ? 'bg-green-100 text-green-800' : 
                                           ($path->status === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($path->status) }}
                                    </span>
                                    <a href="{{ route('paths.show', $path->slug) }}" 
                                       class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                                        Ver detalles
                                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="sm:flex sm:items-center sm:justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Todas las Rutas</h2>
                            <p class="mt-1 text-sm text-gray-600">Explora todas las rutas disponibles</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($regularPaths as $path)
                            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                                <div class="flex items-center space-x-4">
                                    @if($path->getFirstMediaUrl('cover'))
                                        <img src="{{ $path->getFirstMediaUrl('cover') }}" 
                                             alt="{{ $path->name }}" 
                                             class="h-16 w-16 object-cover rounded-lg">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-gray-900 truncate">
                                            {{ $path->name }}
                                        </h3>
                                        @if($path->parent)
                                            <p class="text-sm text-gray-500">
                                                {{ $path->parent->name }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($path->description)
                                    <p class="mt-4 text-gray-600 line-clamp-3">
                                        {{ $path->description }}
                                    </p>
                                @endif

                                <div class="mt-4 flex items-center justify-between">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $path->status === 'published' ? 'bg-green-100 text-green-800' : 
                                           ($path->status === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($path->status) }}
                                    </span>
                                    <a href="{{ route('paths.show', $path->slug) }}" 
                                       class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                                        Ver detalles
                                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 mb-4">
                        {{ $regularPaths->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
