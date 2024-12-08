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
                            <a href="{{ route('admin.contents.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('Volver') }}
                            </a>
                            <a href="#" 
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
                                        <dt class="text-sm font-medium text-gray-500">Autor</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $content->author->name }}</dd>
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

                        <!-- Contenido principal -->
                        <div class="md:col-span-2">
                            <div class="prose prose-slate max-w-none">
                                {!! $html !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
