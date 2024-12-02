<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AreaRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => [
                'nullable',
                'exists:areas,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        // Evitar que un área sea su propio padre
                        if ($this->route('area') && $value == $this->route('area')->id) {
                            $fail('Un área no puede ser su propio padre.');
                        }
                        // Evitar ciclos en la jerarquía
                        if ($this->route('area') && $this->hasCircularReference($this->route('area'), $value)) {
                            $fail('No se pueden crear referencias circulares en la jerarquía.');
                        }
                    }
                }
            ],
            'featured' => ['boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'suspended'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'cover' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:' . env('COVER_MAX_FILE_SIZE', 2048),
                'dimensions:max_width=' . env('COVER_MAX_DIMENSIONS', 2000) . ',max_height=' . env('COVER_MAX_DIMENSIONS', 2000)
            ],
            'meta' => ['nullable', 'array'],
            'meta.title' => ['nullable', 'string', 'max:60'],
            'meta.description' => ['nullable', 'string', 'max:160'],
            'meta.keywords' => ['nullable', 'string', 'max:255'],
        ];

        // Si estamos actualizando, hacemos algunas validaciones condicionales
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'][] = Rule::unique('areas')->ignore($this->route('area'));
        } else {
            $rules['name'][] = 'unique:areas';
        }

        return $rules;
    }

    protected function hasCircularReference($area, $newParentId, $visited = []): bool
    {
        // Si el área que queremos poner como padre es la misma área que estamos editando
        if ($area->id === $newParentId) {
            return true;
        }

        // Si el nuevo padre es uno de los hijos o descendientes del área actual
        $descendants = $area->children()->pluck('id')->toArray();
        if (in_array($newParentId, $descendants)) {
            return true;
        }

        return false;
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
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos :min caracteres.',
            'name.max' => 'El nombre no puede tener más de :max caracteres.',
            'description.max' => 'La descripción no puede tener más de :max caracteres.',
            'parent_id.exists' => 'El área padre seleccionada no existe.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
            'sort_order.integer' => 'El orden debe ser un número entero.',
            'sort_order.min' => 'El orden no puede ser negativo.',
            'cover.file' => 'El archivo debe ser un archivo.',
            'cover.mimes' => 'La imagen debe ser de tipo: :values.',
            'cover.max' => 'La imagen no puede ser mayor a :max kilobytes.',
            'cover.dimensions' => 'La imagen excede las dimensiones máximas permitidas.',
            'meta.array' => 'Los metadatos deben ser un array.',
            'meta.title.max' => 'El título meta no puede tener más de :max caracteres.',
            'meta.description.max' => 'La descripción meta no puede tener más de :max caracteres.',
            'meta.keywords.max' => 'Las palabras clave meta no pueden tener más de :max caracteres.',
        ];
    }
}
