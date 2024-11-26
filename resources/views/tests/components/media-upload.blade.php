@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">{{ __('Test Media Upload') }}</div>

                <div class="card-body">
                    <form id="mediaUploadForm" method="POST" action="{{ route('media.store') }}" enctype="multipart/form-data" class="dropzone">
                        @csrf
                        <div class="dz-message">
                            <div class="text-sm mb-2">{{ __('Arrastra tu imagen aquí o haz clic para seleccionarla') }}</div>
                            <div class="text-xs text-muted">
                                Tipos permitidos: {{ implode(', ', config('media.avatar.allowed_types')) }}<br>
                                Tamaño máximo: {{ config('media.avatar.max_file_size')/1024 }}MB<br>
                                Dimensiones máximas: {{ config('media.avatar.max_dimensions') }}x{{ config('media.avatar.max_dimensions') }} píxeles
                            </div>
                        </div>
                        <input type="hidden" name="model_type" value="App\Models\User">
                        <input type="hidden" name="model_id" value="{{ auth()->id() }}">
                        <input type="hidden" name="collection" value="test">
                    </form>

                    <div class="mt-4 text-right">
                        <button type="submit" form="mediaUploadForm" class="btn btn-primary">
                            {{ __('Subir imagen') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<style>
    .dropzone {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        background: white;
    }
    .dropzone .dz-message {
        margin: 1em 0;
    }
    .text-sm {
        font-size: 0.875rem;
    }
    .text-xs {
        font-size: 0.75rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
    Dropzone.autoDiscover = false;

    const config = {
        maxFileSize: {{ config('media.avatar.max_file_size') }},
        maxDimensions: {{ config('media.avatar.max_dimensions') }},
        allowedTypes: {!! json_encode(config('media.avatar.allowed_types')) !!}
    };

    const dropzone = new Dropzone("#mediaUploadForm", {
        url: "{{ route('media.store') }}",
        maxFiles: 1,
        acceptedFiles: config.allowedTypes.map(type => `.${type}`).join(','),
        maxFilesize: config.maxFileSize / 1024,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        params: {
            model_type: "App\\Models\\User",
            model_id: {{ auth()->id() }},
            collection: "test"
        },
        autoProcessQueue: false,
        addRemoveLinks: true,
        dictRemoveFile: "Eliminar",
        success: function(file, response) {
            if (response.success) {
                console.log('Archivo subido correctamente:', response);
            }
        },
        error: function(file, response) {
            let message = response.message || 'Error al subir el archivo';
            console.error('Error:', message);
        }
    });
</script>
@endpush
