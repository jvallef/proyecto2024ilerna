<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">Contenidos</h2>
                        <a href="{{ route('admin.contents.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Crear Contenido
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Título</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Tipo</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Estado</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Creado</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($contents as $content)
                                    <tr>
                                        <td class="px-6 py-4">{{ $content->title }}</td>
                                        <td class="px-6 py-4">{{ $content->type }}</td>
                                        <td class="px-6 py-4">{{ $content->status }}</td>
                                        <td class="px-6 py-4">{{ $content->created_at->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.contents.show', $content) }}" 
                                               class="text-blue-600 hover:text-blue-900 mr-3"
                                               title="Ver contenido">
                                                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $contents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
