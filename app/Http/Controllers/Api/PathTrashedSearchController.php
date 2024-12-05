<?php

namespace App\Http\Controllers\Api;

use App\Models\Path;

class PathTrashedSearchController extends SearchController
{
    protected function getModelClass(): string
    {
        return Path::class;
    }

    protected function getSearchFields(): array
    {
        return ['name', 'description'];
    }

    protected function formatSuggestion($model): string
    {
        return $model->name;
    }

    protected function additionalConstraints($query)
    {
        // Solo mostrar paths eliminados
        $query->onlyTrashed();

        // Si el usuario no es admin, solo mostrar paths publicados
        if (!auth()->user()?->hasRole('admin')) {
            $query->where('status', 'published');
        }

        return $query;
    }
}
