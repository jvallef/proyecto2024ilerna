<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h2 class="text-2xl font-semibold">Crear Nuevo Contenido</h2>
                        
                        <form id="contentForm" action="{{ route('admin.contents.store') }}" method="POST" enctype="multipart/form-data" class="mt-6">
                            @csrf
                            
                            <div class="mb-4">
                                <textarea id="markdown" name="markdown" 
                                    class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    rows="20">{{ $template }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label for="files" class="block text-sm font-medium text-gray-700">Archivos e Imágenes</label>
                                <input type="file" name="files[]" id="files" 
                                    class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    multiple>
                                <p class="mt-2 text-sm text-gray-500">
                                    Sube tus archivos aquí. Para referenciarlos en el contenido usa:
                                    <br>- Imágenes: ![descripción](!media[nombre-archivo])
                                    <br>- Archivos: !file[nombre-archivo]
                                </p>
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button type="button" onclick="window.location='{{ route('admin.contents.index') }}'">
                                    Cancelar
                                </x-primary-button>
                                <x-secondary-button type="button" onclick="previewContent()">
                                    Vista Previa
                                </x-secondary-button>
                                <x-primary-button type="submit">
                                    Guardar
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Vista Previa -->
    <div id="previewModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg overflow-hidden shadow-xl max-w-4xl w-full">
                <div class="px-6 py-4 bg-gray-100 flex justify-between items-center border-b">
                    <h3 class="text-lg font-medium text-gray-900">Vista Previa</h3>
                    <button type="button" onclick="closePreviewModal()" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Cerrar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto max-h-[calc(100vh-200px)]">
                    <div id="previewContent" class="prose prose-slate max-w-none">
                        <!-- El contenido se insertará aquí -->
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
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        
        fetch('{{ route('admin.contents.preview') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            console.log('Respuesta del servidor:', data); // Debug
            
            if (data.error) {
                alert(data.error);
                return;
            }
            
            document.getElementById('previewContent').innerHTML = data.preview;
            document.getElementById('previewModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al generar la vista previa: ' + error.message);
        });
    }

    function closePreviewModal() {
        document.getElementById('previewModal').classList.add('hidden');
    }
    </script>
    @endpush
</x-app-layout>
