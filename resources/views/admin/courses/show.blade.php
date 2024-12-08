<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="sm:flex sm:items-center sm:justify-between mb-6">
                        <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $course->title }}</h2>
                        <p class="mt-1 text-sm text-gray-600">
                                {{ $course->status }}
                            </p>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('status') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 max-w-lg">
                                <x-search-autocomplete 
                                    :route="route('admin.courses.trashed')"
                                    :search-url="route('admin.api.courses.trashed.search')"
                                    placeholder="No integrado temporalmente..." />
                            </div>
                            <div class="ml-4">
                                <a href="{{ route('admin.courses.index') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3h18" />
                                    </svg>
                                    {{ __('Volver') }}
                                </a>


                                <a href="{{ route('admin.courses.edit', $course) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-secondary hover:bg-secondary/90 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition ease-in-out duration-150 whitespace-nowrap">
                                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    {{ __('Editar curso') }}
                                </a>
                            </div>
                        </div>
                    </div>

<!-- no tocar antes de esta línea -->

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Sidebar con metadatos -->
                        <div class="md:col-span-1">
                            <!-- Imagen del curso -->
                            @if($course->getFirstMediaUrl('cover'))
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold mb-2">{{ __('Imagen de portada') }}</h3>
                                    <img src="{{ $course->getFirstMediaUrl('cover') }}" 
                                         alt="{{ $course->title }}" 
                                         class="w-full rounded-lg shadow">
                                </div>
                            @endif

                            @if($course->getFirstMediaUrl('banner'))
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold mb-2">{{ __('Banner') }}</h3>
                                    <img src="{{ $course->getFirstMediaUrl('banner') }}" 
                                         alt="{{ $course->title }} banner" 
                                         class="w-full rounded-lg shadow">
                                </div>
                            @endif

                            <!-- Otros metadatos -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <h3 class="text-lg font-semibold mb-4">{{ __('Información del curso') }}</h3>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Estado') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $course->status }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Creado') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $course->created_at->format('d/m/Y') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Última actualización') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $course->updated_at->format('d/m/Y') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Contenido principal -->
                        <div class="md:col-span-2">
                            <!-- Descripción -->
                            <div class="prose max-w-none mb-8">
                                <h3 class="text-lg font-semibold mb-4">{{ __('Descripción') }}</h3>
                                {!! $course->description !!}
                            </div>

                            <!-- Contenido del curso -->
                            <div class="bg-white rounded-lg mb-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold">{{ __('Contenido del curso') }}</h3>
                                </div>

                                <!-- Formulario para añadir contenido -->
                                <form action="{{ route('admin.courses.content.add', $course) }}" method="POST" class="mb-6">
                                    @csrf
                                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                                    <div class="flex gap-4">
                                        <div class="flex-1">
                                            <input type="text" name="title" required 
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                   placeholder="{{ __('Título del contenido') }}">
                                        </div>
                                        <button type="submit" 
                                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            {{ __('Añadir') }}
                                        </button>
                                    </div>
                                </form>

                                <!-- Lista de contenidos -->
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Título') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Acciones') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($course->contents as $content)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $content->title }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <div class="flex space-x-3">
                                                            <a href="{{ route('admin.courses.content.edit', ['course' => $course->id, 'content' => $content->id]) }}" 
                                                               class="text-indigo-600 hover:text-indigo-900">
                                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                            </a>
                                                            <form action="{{ route('admin.courses.content.destroy', ['course' => $course->id, 'content' => $content->id]) }}" 
                                                                  method="POST" 
                                                                  class="inline-block">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                        class="text-red-600 hover:text-red-900">
                                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                        {{ __('No hay contenidos disponibles') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="mt-6 flex justify-end space-x-3">
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
