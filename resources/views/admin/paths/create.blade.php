<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <div>
                            <h2 class="text-2xl font-semibold">Crear Nueva Ruta</h2>
                        </div>

                        @include('admin.paths.form', compact('path', 'pathsList'))
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
