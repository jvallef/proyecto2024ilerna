<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h2 class="text-2xl font-semibold">Editar Área: {{ $area->name }}</h2>
                    </div>

                    @include('admin.areas.form', ['area' => $area, 'areasList' => $areasList])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
