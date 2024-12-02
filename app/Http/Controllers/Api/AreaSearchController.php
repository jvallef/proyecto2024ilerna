<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AreaSearchController extends SearchController
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
        if (request()->has('trashed')) {
            $query->onlyTrashed();
        } else {
            $query->whereNull('deleted_at');
        }

        // Si el usuario no es admin, solo mostrar áreas publicadas
        if (!auth()->user()?->hasRole('admin')) {
            $query->where('status', 'published');
        }

        return $query;
    }
}
