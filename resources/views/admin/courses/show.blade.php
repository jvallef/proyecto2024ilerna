<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $course->title }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.courses.edit', $course) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    {{ __('Editar') }}
                </a>
                <form action="{{ route('admin.courses.destroy', $course) }}" 
                      method="POST" 
                      class="inline-block"
                      onsubmit="return confirm('¿Estás seguro de que quieres eliminar este curso?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        {{ __('Eliminar') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Imagen y detalles básicos -->
                        <div class="md:col-span-1">
                            @if($course->getFirstMediaUrl('cover'))
                                <img src="{{ $course->getFirstMediaUrl('cover') }}" 
                                     alt="{{ $course->title }}" 
                                     class="w-full h-auto rounded-lg shadow-md mb-4">
                            @endif
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-lg font-semibold mb-4">Detalles del Curso</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                        <dd class="mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : 
                                                   ($course->status === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($course->status) }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Grupo de Edad</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $course->age_group ?: 'No especificado' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Destacado</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if($course->featured)
                                                <span class="text-green-600">Sí</span>
                                            @else
                                                <span class="text-gray-500">No</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Autor</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if($course->author)
                                                {{ $course->author->name }}
                                                @if(!$course->author_active)
                                                    <span class="text-red-600 text-xs">(Inactivo)</span>
                                                @endif
                                            @else
                                                <span class="text-gray-500">No asignado</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Última actualización</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $course->updated_at->format('d/m/Y H:i') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Contenido principal -->
                        <div class="md:col-span-2">
                            <div class="prose max-w-none">
                                <h2 class="text-2xl font-bold mb-4">{{ $course->title }}</h2>
                                <div class="text-gray-600">
                                    {!! $course->description !!}
                                </div>
                            </div>

                            <!-- Rutas asociadas -->
                            <div class="mt-8">
                                <h3 class="text-lg font-semibold mb-4">Rutas Asociadas</h3>
                                @if($course->paths->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($course->paths as $path)
                                            <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg">
                                                <div>
                                                    <h4 class="font-medium">{{ $path->name }}</h4>
                                                    @if($path->description)
                                                        <p class="text-sm text-gray-600">{{ Str::limit($path->description, 100) }}</p>
                                                    @endif
                                                </div>
                                                <a href="{{ route('admin.paths.show', $path) }}" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    Ver ruta
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500">Este curso no está asociado a ninguna ruta.</p>
                                @endif
                            </div>

                            <!-- Estadísticas -->
                            <div class="mt-8">
                                <h3 class="text-lg font-semibold mb-4">Estadísticas</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-500">Estudiantes Matriculados</dt>
                                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                            {{ $course->enrolledUsers->count() }}
                                        </dd>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <dt class="text-sm font-medium text-gray-500">Contenidos</dt>
                                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                            {{ $course->contents->count() }}
                                        </dd>
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
