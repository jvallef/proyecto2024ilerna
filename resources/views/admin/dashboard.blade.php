<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="sm:flex sm:items-center sm:justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">{{ __('Panel de Administración') }}</h2>
                            <p class="mt-1 text-sm text-gray-600">{{ __('Bienvenido al panel de administración') }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 flex flex-col">
                                <div class="flex-grow">
                                    <div class="flex items-center justify-between mb-2">
                                        <h5 class="font-medium">{{ __('Usuarios') }}</h5>
                                        @if($usersCount > 0)
                                            <span class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-success rounded-full">
                                                {{ $usersCount }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600">{{ __('Gestiona los usuarios del sistema') }}</p>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('admin.users.index') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-secondary hover:bg-secondary/90 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Ver Usuarios') }}
                                    </a>
                                </div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 flex flex-col">
                                <div class="flex-grow">
                                    <div class="flex items-center justify-between mb-2">
                                        <h5 class="font-medium">{{ __('Áreas') }}</h5>
                                        @if($areasCount > 0)
                                            <span class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-success rounded-full">
                                                {{ $areasCount }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">{{ __('Gestiona las áreas de conocimiento y contenido.') }}</p>
                                </div>
                                <div>
                                    <button class="inline-flex items-center px-4 py-2 bg-secondary hover:bg-secondary/90 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Gestionar Áreas') }}
                                    </button>
                                </div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 flex flex-col">
                                <div class="flex-grow">
                                    <div class="flex items-center justify-between mb-2">
                                        <h5 class="font-medium">{{ __('Informes') }}</h5>
                                        @if($reportsCount > 0)
                                            <span class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-success rounded-full">
                                                {{ $reportsCount }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">{{ __('Accede y genera informes del sistema.') }}</p>
                                </div>
                                <div>
                                    <button class="inline-flex items-center px-4 py-2 bg-secondary hover:bg-secondary/90 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Ver Informes') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
