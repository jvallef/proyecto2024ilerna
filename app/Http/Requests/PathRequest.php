<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Path;

class PathRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el middleware del controlador
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'name' => [
                'required', 
                'string', 
                'min:3', 
                'max:255',
                Rule::unique('paths')->ignore($this->route('path')),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => [
                'nullable',
                'exists:paths,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        // Evitar que un path sea su propio padre
                        if ($this->route('path') && $value == $this->route('path')->id) {
                            $fail('Un path no puede ser su propio padre.');
                        }
                        // Evitar ciclos en la jerarquía
                        if ($this->route('path') && $this->hasCircularReference($this->route('path'), $value)) {
                            $fail('No se pueden crear referencias circulares en la jerarquía.');
                        }
                        // Verificar que el padre pertenece a la misma área
                        $parentPath = Path::find($value);
                        if ($parentPath && isset($this->area_id) && $parentPath->area_id != $this->area_id) {
                            $fail('El path padre debe pertenecer a la misma área.');
                        }
                    }
                }
            ],
            'area_id' => ['required', 'exists:areas,id'],
            'featured' => ['boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'suspended'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'cover' => [
                'nullable',
                'file',
                'mimes:' . implode(',', config('media.cover.allowed_types')),
                'max:' . config('media.cover.max_file_size'),
                'dimensions:max_width=' . config('media.cover.max_dimensions') . ',max_height=' . config('media.cover.max_dimensions')
            ],
            'meta' => ['nullable', 'array'],
            'meta.title' => ['nullable', 'string', 'max:60'],
            'meta.description' => ['nullable', 'string', 'max:160'],
            'meta.keywords' => ['nullable', 'string', 'max:255'],
        ];

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos :min caracteres.',
            'name.max' => 'El nombre no puede tener más de :max caracteres.',
            'name.unique' => 'Ya existe un path con este nombre.',
            'description.max' => 'La descripción no puede tener más de :max caracteres.',
            'parent_id.exists' => 'El path padre seleccionado no existe.',
            'area_id.required' => 'El área es obligatoria.',
            'area_id.exists' => 'El área seleccionada no existe.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
            'cover.file' => 'La portada debe ser un archivo.',
            'cover.mimes' => 'La portada debe ser una imagen en formato: :values.',
            'cover.max' => 'La portada no puede ser mayor a :max kilobytes.',
            'cover.dimensions' => 'Las dimensiones de la portada no son válidas.',
            'meta.title.max' => 'El título meta no puede tener más de :max caracteres.',
            'meta.description.max' => 'La descripción meta no puede tener más de :max caracteres.',
            'meta.keywords.max' => 'Las palabras clave meta no pueden tener más de :max caracteres.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('featured')) {
            $this->merge([
                'featured' => $this->featured === 'true' || $this->featured === '1' || $this->featured === true,
            ]);
        }

        if ($this->has('meta')) {
            $meta = is_array($this->meta) ? $this->meta : json_decode($this->meta, true);
            $this->merge(['meta' => $meta]);
        }
    }

    /**
     * Verifica si existe una referencia circular en la jerarquía de paths.
     */
    protected function hasCircularReference(Path $path, int $parentId): bool
    {
        $currentParent = Path::find($parentId);
        while ($currentParent) {
            if ($currentParent->id === $path->id) {
                return true;
            }
            $currentParent = $currentParent->parent;
        }
        return false;
    }
}
