<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $course->name }}
            </h2>
            @can('manage courses')
                <div class="flex space-x-4">
                    <a href="{{ route('admin.courses.edit', $course) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        {{ __('Editar') }}
                    </a>
                </div>
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:space-x-8">
                        <!-- Imagen y detalles principales -->
                        <div class="md:w-1/3">
                            @if($course->getFirstMediaUrl('cover'))
                                <img src="{{ $course->getFirstMediaUrl('cover') }}" 
                                     alt="{{ $course->name }}" 
                                     class="w-full h-auto rounded-lg shadow-md">
                            @endif
                            <div class="mt-4">
                                <h3 class="text-lg font-semibold">Detalles del Curso</h3>
                                <dl class="mt-2 space-y-2">
                                    @if($course->path)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Ruta</dt>
                                            <dd class="mt-1">
                                                <a href="{{ route('paths.show', $course->path->slug) }}" 
                                                   class="text-blue-600 hover:text-blue-500">
                                                    {{ $course->path->name }}
                                                </a>
                                            </dd>
                                        </div>
                                    @endif
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
                                </dl>
                            </div>
                        </div>

                        <!-- Contenido principal -->
                        <div class="md:w-2/3 mt-6 md:mt-0">
                            <div class="prose max-w-none">
                                <h2 class="text-2xl font-bold mb-4">{{ $course->name }}</h2>
                                <div class="text-gray-600">
                                    {!! $course->description !!}
                                </div>
                            </div>

                            @if($course->status === 'published')
                                <div class="mt-8">
                                    @auth
                                        @if(auth()->user()->isEnrolledIn($course))
                                            <a href="{{ route('courses.progress', $course->slug) }}" 
                                               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                                Continuar Curso
                                            </a>
                                        @else
                                            <form action="{{ route('courses.enroll', $course->slug) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                                                    Matricularme
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" 
                                           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                            Inicia sesión para matricularte
                                        </a>
                                    @endauth
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
