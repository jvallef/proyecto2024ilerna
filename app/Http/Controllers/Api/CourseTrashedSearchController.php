<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;

class CourseTrashedSearchController extends SearchController
{
    protected function getModelClass(): string
    {
        return Course::class;
    }

    protected function getSearchFields(): array
    {
        return ['title', 'description'];
    }

    protected function formatSuggestion($model): string
    {
        return $model->title;
    }

    protected function additionalConstraints($query)
    {
        // Solo mostrar cursos eliminados
        $query->onlyTrashed();

        // Si el usuario no es admin, solo mostrar cursos publicados
        if (!auth()->user()?->hasRole('admin')) {
            $query->where('status', 'published');
        }

        return $query;
    }
}
