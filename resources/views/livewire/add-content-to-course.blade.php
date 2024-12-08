<div class="p-6">
    <h2 class="text-lg font-medium text-gray-900 mb-4">
        Añadir contenido al curso
    </h2>

    <div class="mb-4">
        <input 
            wire:model.debounce.300ms="search"
            type="text"
            class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
            placeholder="Buscar contenidos..."
        >
    </div>

    <div class="space-y-4">
        @forelse ($contents as $content)
            <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow">
                <div>
                    <h3 class="text-sm font-medium text-gray-900">{{ $content->title }}</h3>
                    <p class="text-sm text-gray-500">{{ $content->type }}</p>
                </div>
                <button
                    wire:click="addContent({{ $content->id }})"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Añadir
                </button>
            </div>
        @empty
            <div class="text-center text-gray-500">
                No se encontraron contenidos disponibles.
            </div>
        @endforelse
    </div>
</div>
