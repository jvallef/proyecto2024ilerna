<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="sm:flex sm:items-center sm:justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Nuevo Usuario</h2>
                            <p class="mt-1 text-sm text-gray-600">Crea un nuevo usuario en el sistema</p>
                        </div>
                    </div>

                    @include('admin.users.form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
