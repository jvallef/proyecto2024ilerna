<?php
/**
 * Es una versión más simple de
 * https://medium.com/@lordNeic/generate-unique-slugs-on-the-fly-for-create-and-update-in-laravel-models-87ac7e52aa1e
 */
namespace App\Traits;

use Illuminate\Support\Str;
use App\Models\Area;

trait GeneratesSlug
{
    public function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (Area::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
