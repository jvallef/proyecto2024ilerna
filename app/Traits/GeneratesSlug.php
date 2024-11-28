<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GeneratesSlug
{
    /**
     * Generate a unique slug for the model.
     *
     * @param string|null $name The string to generate the slug from
     * @param string|null $field The field to use for generating the slug (default: 'name')
     * @param string|null $slugField The field to store the slug (default: 'slug')
     * @return string
     */
    public function generateUniqueSlug(?string $name = null, ?string $field = null, ?string $slugField = 'slug'): string
    {
        // Si no es un modelo Eloquent, simplemente devolver el slug básico
        if (!($this instanceof Model)) {
            return Str::slug($name ?? '');
        }

        // Determine the source field (name or title)
        if ($field === null) {
            $field = $this->hasAttribute('name') ? 'name' : 'title';
        }

        // Get the name from the model if not provided
        if ($name === null) {
            $name = $this->{$field};
        }

        if (!$name) {
            throw new \InvalidArgumentException("No value found for slug generation from field '{$field}'");
        }

        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        // Get the model class for querying
        $model = get_class($this);

        // Build the query to check for existing slugs
        $query = $model::where($slugField, $slug);
        
        // If the model has an ID (updating), exclude the current model
        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        // Keep incrementing the counter until we find a unique slug
        while ($query->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
            $query = $model::where($slugField, $slug);
            if ($this->exists) {
                $query->where('id', '!=', $this->id);
            }
        }

        return $slug;
    }

    /**
     * Boot the trait.
     * Automatically generates a slug when creating or updating the model.
     */
    protected static function bootGeneratesSlug(): void
    {
        // Solo registrar el evento saving si la clase que usa el trait es un modelo Eloquent
        if (is_subclass_of(static::class, Model::class)) {
            static::saving(function (Model $model) {
                $field = $model->hasAttribute('name') ? 'name' : 'title';
                
                // Only generate slug if the name/title has changed or slug is empty
                if ($model->isDirty($field) || empty($model->slug)) {
                    $model->slug = $model->generateUniqueSlug(null, $field);
                }
            });
        }
    }

    /**
     * Check if the model has a specific attribute.
     *
     * @param mixed $key
     * @return bool
     */
    public function hasAttribute($key): bool
    {
        // Solo verificar atributos si es un modelo Eloquent
        if ($this instanceof Model) {
            return array_key_exists($key, $this->attributes);
        }
        return false;
    }
}
