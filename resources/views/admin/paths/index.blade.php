<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="sm:flex sm:items-center sm:justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">{{ __('Rutas') }}</h2>
                            <p class="mt-1 text-sm text-gray-600">{{ __('Gestiona las rutas del sistema') }}</p>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('status') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="mb-6">
                        <!-- Contenedor flexible que cambia a columna en móvil -->
                        <div class="flex flex-col space-y-4 md:flex-row md:space-y-0 md:items-center md:justify-between">
                            <!-- Campo de búsqueda arriba en móvil, izquierda en desktop -->
                            <div class="w-full md:max-w-lg order-1">
                                <x-search-autocomplete 
                                    :route="route('admin.paths.index')"
                                    :search-url="route('admin.api.paths.search')"
                                    placeholder="Buscar por nombre..." />
                            </div>
                            <!-- Botones abajo en móvil, derecha en desktop -->
                            <div class="flex items-center justify-center space-x-2 order-2 md:ml-6">
                                <a href="{{ route('admin.paths.trashed') }}" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white uppercase hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 whitespace-nowrap">
                                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                    Papelera
                                </a>
                                <a href="{{ route('admin.paths.create') }}" 
                                class="inline-flex items-center px-4 py-2 bg-secondary hover:bg-secondary/90 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition ease-in-out duration-150 whitespace-nowrap">
                                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Nueva Ruta
                                </a>
                            </div>
                        </div>
                    </div>

                    @if($featuredPaths->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Rutas Destacadas') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($featuredPaths as $path)
                                    <div class="bg-white border-2 border-primary/50 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 p-4">
                                        <div class="flex items-center">
                                            @if($path->getFirstMediaUrl('cover'))
                                                <img class="h-12 w-12 rounded-lg object-cover" 
                                                     src="{{ $path->getFirstMediaUrl('cover') }}" 
                                                     alt="{{ $path->name }}">
                                            @endif
                                            <div class="ml-3">
                                                <h4 class="text-sm font-medium text-gray-900 flex items-center">
                                                    {{ $path->name }}
                                                    <svg class="h-4 w-4 ml-1 text-primary" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                                    </svg>
                                                </h4>
                                                @if($path->parent)
                                                    <p class="text-xs text-gray-500">{{ $path->parent->name }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <x-path-actions :path="$path" :render-modal="false" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Ruta') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Ruta Padre') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Estado') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Orden') }}
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">{{ __('Acciones') }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($regularPaths as $path)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($path->getFirstMediaUrl('cover'))
                                                    <img class="h-10 w-10 rounded-lg object-cover" 
                                                         src="{{ $path->getFirstMediaUrl('cover') }}" 
                                                         alt="{{ $path->name }}">
                                                @endif
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900 flex items-center">
                                                        {{ $path->name }}
                                                        @if($path->featured)
                                                            <svg class="h-4 w-4 ml-1 text-primary" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                                            </svg>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($path->parent)
                                                <div class="text-sm text-gray-900">{{ $path->parent->name }}</div>
                                            @else
                                                <span class="text-sm text-gray-500">{{ __('Ninguna') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-path-status :status="$path->status" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $path->sort_order }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <x-path-actions :path="$path" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $regularPaths->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
