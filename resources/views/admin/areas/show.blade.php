<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Encabezado -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">{{ $area->name }}</h2>
                        <div class="flex space-x-4">
                            <a href="{{ route('admin.areas.edit', $area) }}" 
                               class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Editar
                            </a>
                            <a href="{{ route('admin.areas.index') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Volver
                            </a>
                        </div>
                    </div>

                    <!-- Información principal -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Columna izquierda -->
                        <div class="space-y-6">
                            <!-- Imagen -->
                            @if($area->getFirstMediaUrl('areas'))
                                <div>
                                    <h3 class="text-lg font-medium mb-2">Imagen</h3>
                                    <img src="{{ $area->getFirstMediaUrl('areas') }}" 
                                         alt="{{ $area->name }}"
                                         class="rounded-lg max-w-full h-auto">
                                </div>
                            @endif

                            <!-- Descripción -->
                            <div>
                                <h3 class="text-lg font-medium mb-2">Descripción</h3>
                                <p class="text-gray-600">
                                    {{ $area->description ?: 'Sin descripción' }}
                                </p>
                            </div>

                            <!-- Información adicional -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-lg font-medium mb-2">Estado</h3>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($area->status === 'published') bg-green-100 text-green-800 
                                        @elseif($area->status === 'draft') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($area->status) }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium mb-2">Destacado</h3>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $area->featured ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $area->featured ? 'Sí' : 'No' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha -->
                        <div class="space-y-6">
                            <!-- Jerarquía -->
                            <div>
                                <h3 class="text-lg font-medium mb-2">Jerarquía</h3>
                                @if($area->parent)
                                    <div class="mb-4">
                                        <span class="text-gray-600">Área padre:</span>
                                        <a href="{{ route('admin.areas.show', $area->parent) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 ml-2">
                                            {{ $area->parent->name }}
                                        </a>
                                    </div>
                                @endif

                                @if($area->children->count() > 0)
                                    <div>
                                        <span class="text-gray-600">Subáreas:</span>
                                        <ul class="list-disc list-inside mt-2 space-y-1">
                                            @foreach($area->children as $child)
                                                <li>
                                                    <a href="{{ route('admin.areas.show', $child) }}"
                                                       class="text-indigo-600 hover:text-indigo-900">
                                                        {{ $child->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            <!-- Metadatos SEO -->
                            <div>
                                <h3 class="text-lg font-medium mb-2">Metadatos SEO</h3>
                                <div class="space-y-4">
                                    <div>
                                        <span class="text-gray-600">Título:</span>
                                        <p>{{ $area->meta['seo_title'] ?? 'No definido' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Descripción:</span>
                                        <p>{{ $area->meta['seo_description'] ?? 'No definida' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Palabras clave:</span>
                                        <p>{{ $area->meta['seo_keywords'] ?? 'No definidas' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del sistema -->
                            <div class="pt-6 border-t">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Creado:</span>
                                        <p>{{ $area->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Última actualización:</span>
                                        <p>{{ $area->updated_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Creado por:</span>
                                        <p>{{ $area->user->name }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Orden:</span>
                                        <p>{{ $area->sort_order }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
