<div class="p-6">
    <h2 class="text-lg font-medium text-gray-900 mb-4">
        Eliminar contenido del curso
    </h2>

    <p class="mb-4 text-sm text-gray-600">
        ¿Estás seguro de que deseas eliminar el contenido "{{ $contentTitle }}" de este curso?
    </p>

    <div class="mt-6 flex justify-end space-x-3">
        <button
            wire:click="$emit('closeModal')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
            Cancelar
        </button>
        <button
            wire:click="removeContent"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >
            Eliminar
        </button>
    </div>
</div>
