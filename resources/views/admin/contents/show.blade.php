<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="sm:flex sm:items-center sm:justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">{{ $content->title }}</h2>
                            <p class="mt-1 text-sm text-gray-600">{{ ucfirst($content->type) }} - {{ ucfirst($content->status) }}</p>
                        </div>
                        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none space-x-2">
                            <a href="{{ route('admin.courses.show', $course) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('Volver al curso') }}
                            </a>
                            <a href="{{ route('admin.courses.content.edit', ['course' => $course, 'content' => $content]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                {{ __('Editar') }}
                            </a>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('status') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Contenido principal -->
                        <div class="md:col-span-2">
                            <div class="bg-white rounded-lg p-4">
                                <h3 class="text-lg font-semibold mb-4">Contenido</h3>
                                <div class="prose prose-sm sm:prose lg:prose-lg xl:prose-xl max-w-none">
                                    {!! $html !!}
                                </div>

                                @if($content->getMedia('contents')->count() > 0)
                                    <div class="mt-6">
                                        <h3 class="text-lg font-semibold mb-4">Archivos adjuntos</h3>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($content->getMedia('contents') as $media)
                                                <div class="border rounded-lg p-4">
                                                    @if(str_contains($media->mime_type, 'image'))
                                                        <img src="{{ $media->getUrl() }}" alt="{{ $media->name }}" 
                                                             class="w-full h-32 object-cover rounded-lg mb-2">
                                                    @else
                                                        <div class="w-full h-32 flex items-center justify-center bg-gray-100 rounded-lg mb-2">
                                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <div class="text-sm">
                                                        <p class="font-medium truncate">{{ $media->name }}</p>
                                                        <p class="text-gray-500 text-xs">{{ human_filesize($media->size) }}</p>
                                                        <div class="mt-2 flex space-x-2">
                                                            <a href="{{ $media->getUrl() }}" target="_blank"
                                                               class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                                Ver
                                                            </a>
                                                            <a href="{{ $media->getUrl() }}" download
                                                               class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                                Descargar
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($content->courses->count() > 0)
                                    <div class="mt-6">
                                        <h3 class="text-lg font-semibold mb-4">Cursos asociados</h3>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($content->courses as $course)
                                                <div class="border rounded-lg p-4">
                                                    <h4 class="font-medium">{{ $course->title }}</h4>
                                                    <p class="text-sm text-gray-500">{{ $course->description }}</p>
                                                    <a href="{{ route('admin.courses.show', $course) }}" 
                                                       class="mt-2 inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                        Ver curso
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Metadatos y archivos -->
                        <div class="md:col-span-1">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-lg font-semibold mb-4">Detalles del Contenido</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($content->type) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($content->status) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Fecha de creación</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $content->created_at->format('d/m/Y H:i') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Última actualización</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $content->updated_at->format('d/m/Y H:i') }}</dd>
                                    </div>
                                </dl>
                            </div>

                            @if($content->getMedia('content-files')->count() > 0)
                            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                                <h3 class="text-lg font-semibold mb-4">Archivos adjuntos</h3>
                                <ul class="space-y-2">
                                    @foreach($content->getMedia('content-files') as $media)
                                    <li>
                                        <a href="{{ $media->getUrl() }}" 
                                           class="text-blue-600 hover:text-blue-800 flex items-center text-sm"
                                           target="_blank">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            {{ $media->name }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
