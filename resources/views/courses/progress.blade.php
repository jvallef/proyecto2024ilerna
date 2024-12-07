<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $course->name }} - Progreso
            </h2>
            <a href="{{ route('courses.show', $course->slug) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('Volver al Curso') }}
            </a>
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
                    <!-- Barra de progreso general -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Progreso General</h3>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $progress }}%"></div>
                        </div>
                        <p class="mt-2 text-sm text-gray-600">Has completado el {{ $progress }}% del curso</p>
                    </div>

                    <!-- Lista de lecciones -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-semibold">Lecciones</h3>
                        @forelse($course->lessons as $lesson)
                            <div class="border rounded-lg p-4 {{ $lesson->isCompletedBy(auth()->user()) ? 'bg-green-50' : 'bg-white' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        @if($lesson->isCompletedBy(auth()->user()))
                                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @else
                                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                        <div>
                                            <h4 class="text-lg font-medium">{{ $lesson->title }}</h4>
                                            @if($lesson->description)
                                                <p class="text-sm text-gray-600">{{ $lesson->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-4">
                                        @if($lesson->isCompletedBy(auth()->user()))
                                            <form action="{{ route('courses.lessons.uncomplete', [$course->slug, $lesson->id]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-sm text-gray-600 hover:text-gray-900">
                                                    Marcar como incompleta
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('courses.lessons.complete', [$course->slug, $lesson->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-sm text-blue-600 hover:text-blue-900">
                                                    Marcar como completa
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <a href="{{ route('courses.lessons.show', [$course->slug, $lesson->slug]) }}" 
                                           class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                                            Ver lección
                                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">No hay lecciones disponibles en este curso.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
