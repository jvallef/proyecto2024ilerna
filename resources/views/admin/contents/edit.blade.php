<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h2 class="text-2xl font-semibold">Editar Contenido</h2>
                        
                        <form id="contentForm" action="{{ route('admin.courses.content.update', ['course' => $course_id, 'content' => $content]) }}" method="POST" enctype="multipart/form-data" class="mt-6">
                            @csrf
                            @method('PUT')
                            
                            <input type="hidden" name="course_id" value="{{ $course_id }}">
                            
                            <div class="mb-4">
                                <textarea id="markdown" name="markdown" 
                                    class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    rows="20">{{ $markdown }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label for="files" class="block text-sm font-medium text-gray-700">Archivos e Imágenes</label>
                                <input type="file" name="files[]" id="files" 
                                    class="mt-1 block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100" 
                                    multiple>
                            </div>

                            @if($content->getMedia('content-files')->count() > 0)
                            <div class="mb-4">
                                <h3 class="text-lg font-medium text-gray-700 mb-2">Archivos Actuales</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($content->getMedia('content-files') as $media)
                                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                                        @if(str_starts_with($media->mime_type, 'image/'))
                                            <div class="w-20 h-20 flex-shrink-0 mr-4 overflow-hidden bg-gray-100 rounded-lg">
                                                <img src="{{ $media->getUrl() }}" 
                                                     alt="{{ $media->name }}"
                                                     class="w-full h-full object-cover hover:opacity-75 transition-opacity duration-150"
                                                     onclick="window.open('{{ $media->getUrl() }}', '_blank')"
                                                     title="Click para ver en tamaño completo">
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $media->file_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $media->human_readable_size }}</p>
                                            <p class="text-xs text-gray-500">{{ $media->mime_type }}</p>
                                            <a href="{{ $media->getUrl() }}" target="_blank" 
                                               class="mt-2 inline-flex text-sm text-indigo-600 hover:text-indigo-900">
                                                {{ str_starts_with($media->mime_type, 'image/') ? 'Ver imagen completa' : 'Descargar archivo' }}
                                            </a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="flex justify-between">
                                <button type="submit" 
                                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Guardar Cambios
                                </button>
                                
                                <a href="{{ route('admin.courses.show', $course_id) }}" 
                                   class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewContent() {
            const markdownContent = document.getElementById('markdown').value;
            console.log('Contenido a enviar:', markdownContent); // Debug

            const formData = new FormData();
            formData.append('markdown', markdownContent);

            fetch('{{ route('admin.contents.preview.test') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('preview').innerHTML = data.preview;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
    @endpush
</x-app-layout>
