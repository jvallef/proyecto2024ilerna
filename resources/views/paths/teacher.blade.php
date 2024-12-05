<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold mb-4">{{ __('Rutas') }}</h2>
                    </div>

                    <div class="space-y-6">
                        @foreach($paths as $path)
                            <div class="bg-white rounded-lg shadow p-6">
                                <h3 class="text-xl font-semibold mb-2">{{ $path->name }}</h3>
                                <p class="text-gray-600 mb-4">{{ $path->description }}</p>
                                <div class="flex justify-between items-center text-sm text-gray-500">
                                    <span>
                                        {{ __('Creado el') }}: {{ $path->created_at->format(__('d/m/Y')) }}
                                    </span>
                                    <a href="{{ route('paths.show', $path->slug) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        {{ __('Ver detalles') }} →
                                    </a>
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-6">
                            {{ $paths->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
