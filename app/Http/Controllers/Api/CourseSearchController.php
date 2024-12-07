<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseSearchController extends SearchController
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
        // Si el usuario no es admin, solo mostrar cursos publicados
        if (!auth()->user()?->hasRole('admin')) {
            $query->where('status', 'published');
        }

        return $query;
    }
}
