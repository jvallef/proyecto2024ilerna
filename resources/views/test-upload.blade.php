<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Test Upload') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Subir Imagen</h3>
                        <livewire:file-uploader type="picture" :multiple="false" />
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Subir Múltiples Imágenes</h3>
                        <livewire:file-uploader type="picture" :multiple="true" />
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Subir Documento</h3>
                        <livewire:file-uploader type="file" :multiple="false" />
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Subir Múltiples Documentos</h3>
                        <livewire:file-uploader type="file" :multiple="true" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
