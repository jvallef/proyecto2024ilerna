<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h2 class="text-2xl font-semibold">{{ $user->name }}</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información básica -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium mb-2">Información básica</h3>
                                <div class="space-y-2">
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Email:</span>
                                        <span class="ml-2">{{ $user->email }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Fecha de registro:</span>
                                        <span class="ml-2">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Roles -->
                            <div>
                                <h3 class="text-lg font-medium mb-2">Roles</h3>
                                <div class="space-x-2">
                                    @foreach($user->roles as $role)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Actividad -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium mb-2">Actividad reciente</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Sección en desarrollo...
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="mt-8 flex space-x-4">
                        <a href="{{ route('admin.users.edit', $user) }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            {{ __('Editar Usuario') }}
                        </a>
                        <a href="{{ route('admin.users.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            {{ __('Volver al listado') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
