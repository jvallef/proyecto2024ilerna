<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Cabecera con título y botones -->
                    <div class="sm:flex sm:items-center sm:justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">{{ $course->title }}</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $course->status }}
                            </p>
                        </div>
                        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                            <a href="{{ route('admin.courses.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('Volver al listado') }}
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Sidebar con metadatos -->
                        <div class="md:col-span-1">
                            <!-- Imagen del curso -->
                            @if($course->getFirstMediaUrl('cover'))
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold mb-2">{{ __('Imagen de portada') }}</h3>
                                    <img src="{{ $course->getFirstMediaUrl('cover') }}" 
                                         alt="{{ $course->title }}" 
                                         class="w-full rounded-lg shadow">
                                </div>
                            @endif

                            @if($course->getFirstMediaUrl('banner'))
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold mb-2">{{ __('Banner') }}</h3>
                                    <img src="{{ $course->getFirstMediaUrl('banner') }}" 
                                         alt="{{ $course->title }} banner" 
                                         class="w-full rounded-lg shadow">
                                </div>
                            @endif

                            <!-- Otros metadatos -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <h3 class="text-lg font-semibold mb-4">{{ __('Información del curso') }}</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Estado') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $course->status }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Creado') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $course->created_at->format('d/m/Y') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Última actualización') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $course->updated_at->format('d/m/Y') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Contenido principal -->
                        <div class="md:col-span-2">
                            <!-- Descripción -->
                            <div class="prose max-w-none mb-8">
                                <h3 class="text-lg font-semibold mb-4">{{ __('Descripción') }}</h3>
                                {!! $course->description !!}
                            </div>

                            <!-- Contenido del curso -->
                            <div class="bg-white rounded-lg mb-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold">{{ __('Contenido del curso') }}</h3>
                                </div>

                                <!-- Formulario para añadir contenido -->
                                <form action="{{ route('admin.courses.content.add', $course) }}" method="POST" class="mb-6">
                                    @csrf
                                    <div class="flex gap-4">
                                        <div class="flex-1">
                                            <select name="path_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="">{{ __('Seleccionar path') }}</option>
                                                @foreach($paths as $path)
                                                    <option value="{{ $path->id }}">{{ $path->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex-1">
                                            <select name="area_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="">{{ __('Seleccionar área') }}</option>
                                                @foreach($areas as $area)
                                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" 
                                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            {{ __('Añadir') }}
                                        </button>
                                    </div>
                                </form>

                                <!-- Lista de contenido -->
                                <div class="space-y-4">
                                    @foreach($course->paths as $path)
                                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                                            <div>
                                                <h4 class="font-medium">{{ $path->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ optional($path->area)->name }}</p>
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <a href="{{ route('admin.paths.show', $path) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('admin.courses.content.remove', $course) }}" 
                                                      method="POST" 
                                                      class="inline">
                                                    @csrf
                                                    <input type="hidden" name="path_id" value="{{ $path->id }}">
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('{{ __('¿Estás seguro de querer eliminar este contenido?') }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('admin.courses.edit', $course) }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            {{ __('Editar curso') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
