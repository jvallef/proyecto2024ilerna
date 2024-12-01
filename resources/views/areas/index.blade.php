<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Areas') }}
            </h2>
            @can('manage areas')
            <a href="{{ route('admin.areas.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                {{ __('Nueva Área') }}
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
                            :route="route('areas.index')"
                            :search-url="route('api.areas.search.public')"
                            placeholder="Buscar por nombre o descripción..."
                            :min-chars="2" />
                    </div>
                </div>
            </div>

            @if($featuredAreas->count() > 0)
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold mb-4">Áreas Destacadas</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($featuredAreas as $area)
                            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                                <div class="flex items-center space-x-4">
                                    @if($area->getFirstMediaUrl('areas'))
                                        <img src="{{ $area->getFirstMediaUrl('areas') }}" 
                                             alt="{{ $area->name }}" 
                                             class="h-16 w-16 object-cover rounded-lg">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-gray-900 truncate">
                                            {{ $area->name }}
                                        </h3>
                                        @if($area->parent)
                                            <p class="text-sm text-gray-500">
                                                {{ $area->parent->name }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($area->description)
                                    <p class="mt-4 text-gray-600 line-clamp-3">
                                        {{ $area->description }}
                                    </p>
                                @endif

                                <div class="mt-4 flex items-center justify-between">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $area->status === 'published' ? 'bg-green-100 text-green-800' : 
                                           ($area->status === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($area->status) }}
                                    </span>
                                    <a href="{{ route('areas.show', $area->slug) }}" 
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
                            <h2 class="text-xl font-semibold text-gray-900">Todas las Áreas</h2>
                            <p class="mt-1 text-sm text-gray-600">Explora todas las áreas disponibles</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($regularAreas as $area)
                            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                                <div class="flex items-center space-x-4">
                                    @if($area->getFirstMediaUrl('areas'))
                                        <img src="{{ $area->getFirstMediaUrl('areas') }}" 
                                             alt="{{ $area->name }}" 
                                             class="h-16 w-16 object-cover rounded-lg">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-gray-900 truncate">
                                            {{ $area->name }}
                                        </h3>
                                        @if($area->parent)
                                            <p class="text-sm text-gray-500">
                                                {{ $area->parent->name }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($area->description)
                                    <p class="mt-4 text-gray-600 line-clamp-3">
                                        {{ $area->description }}
                                    </p>
                                @endif

                                <div class="mt-4 flex items-center justify-between">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $area->status === 'published' ? 'bg-green-100 text-green-800' : 
                                           ($area->status === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($area->status) }}
                                    </span>
                                    <a href="{{ route('areas.show', $area->slug) }}" 
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
                        {{ $regularAreas->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
