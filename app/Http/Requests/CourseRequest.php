<?php

namespace App\Http\Requests;

use App\Enums\AgeGroup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $course = $this->route('course');
        $courseId = $course ? $course->id : null;

        $rules = [
            'title' => [
                'required', 
                'string', 
                'min:3',
                'max:255',
                Rule::unique('courses', 'title')->ignore($courseId)
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'featured' => ['boolean'],
            'age_group' => ['nullable', Rule::in(AgeGroup::values())],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'paths' => ['array'],
            'paths.*' => ['exists:paths,id'],
            'contents' => ['array'],
            'contents.*' => ['exists:contents,id'],
            'meta' => ['nullable', 'array'],
            'meta.title' => ['nullable', 'string', 'max:60'],
            'meta.description' => ['nullable', 'string', 'max:160'],
            'meta.keywords' => ['nullable', 'string', 'max:255'],
        ];

        // Reglas para medios
        if ($this->isMethod('POST') || $this->has('cover')) {
            $rules['cover'] = [
                'nullable',
                'image',
                'mimes:' . implode(',', config('media.cover.allowed_types')),
                'max:' . config('media.cover.max_file_size'),
                'dimensions:max_width=' . config('media.cover.max_dimensions') . 
                          ',max_height=' . config('media.cover.max_dimensions'),
            ];
        }

        if ($this->isMethod('POST') || $this->has('banner')) {
            $rules['banner'] = [
                'nullable',
                'image',
                'mimes:' . implode(',', config('media.banner.allowed_types')),
                'max:' . config('media.banner.max_file_size'),
                'dimensions:max_width=' . config('media.banner.max_dimensions') . 
                          ',max_height=' . config('media.banner.max_dimensions'),
            ];
        }

        if ($this->has('files')) {
            $rules['files'] = ['array'];
            $rules['files.*'] = [
                'file',
                'max:' . config('media.max_file_size'),
            ];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.min' => 'El título debe tener al menos :min caracteres.',
            'title.max' => 'El título no puede tener más de :max caracteres.',
            'title.unique' => 'Ya existe un curso con este título.',
            'description.required' => 'La descripción es obligatoria.',
            'description.max' => 'La descripción no puede tener más de :max caracteres.',
            'age_group.in' => 'El grupo de edad seleccionado no es válido.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
            'paths.array' => 'Los paths deben ser una lista.',
            'paths.*.exists' => 'Uno de los paths seleccionados no existe.',
            'banner.dimensions' => $this->getBannerDimensionsMessage(),
            'banner.max' => 'El banner no puede ser más grande de :max kilobytes.',
            'banner.mimes' => 'El banner debe ser una imagen de tipo: :values.',
            'cover.image' => 'El archivo debe ser una imagen.',
            'cover.mimes' => 'La imagen debe ser de tipo: :values.',
            'cover.max' => 'La imagen no puede ser mayor a :max kilobytes.',
            'cover.dimensions' => 'Las dimensiones de la imagen no son válidas.',
            'files.array' => 'Los archivos deben ser una lista.',
            'files.*.file' => 'El archivo no es válido.',
            'files.*.max' => 'El archivo no puede ser mayor a :max kilobytes.',
            'meta.title.max' => 'El título meta no puede tener más de :max caracteres.',
            'meta.description.max' => 'La descripción meta no puede tener más de :max caracteres.',
            'meta.keywords.max' => 'Las palabras clave meta no pueden tener más de :max caracteres.',
        ];
    }

    protected function getBannerDimensionsMessage(): string
    {
        if (!$this->hasFile('banner')) {
            return 'Las dimensiones del banner no son válidas.';
        }

        $image = $this->file('banner');
        $imageSize = getimagesize($image->getPathname());
        if (!$imageSize) {
            return 'No se pudieron determinar las dimensiones de la imagen.';
        }

        $width = $imageSize[0];
        $height = $imageSize[1];
        $maxWidth = config('media.banner.max_dimensions', 1920);
        $maxHeight = config('media.banner.max_dimensions', 1920);

        return sprintf(
            'Las dimensiones del banner deben ser como máximo %dx%d píxeles (recomendado 1920x400). La imagen proporcionada tiene %dx%d pixels.',
            $maxWidth,
            $maxHeight,
            $width,
            $height
        );
    }
}
