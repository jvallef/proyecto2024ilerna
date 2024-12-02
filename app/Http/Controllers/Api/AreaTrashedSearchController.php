<?php

namespace App\Http\Controllers\Api;

use App\Models\Area;

class AreaTrashedSearchController extends SearchController
{
    protected function getModelClass(): string
    {
        return Area::class;
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
        // Solo mostrar áreas eliminadas
        $query->onlyTrashed();

        // Si el usuario no es admin, solo mostrar áreas publicadas
        if (!auth()->user()?->hasRole('admin')) {
            $query->where('status', 'published');
        }

        return $query;
    }
}
