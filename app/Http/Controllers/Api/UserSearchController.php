<?php

namespace App\Http\Controllers\Api;

use App\Models\User;

class UserSearchController extends SearchController
{
    protected function getModelClass(): string
    {
        return User::class;
    }

    protected function getSearchFields(): array
    {
        return ['name', 'email'];
    }

    protected function formatSuggestion($model): string
    {
        return "{$model->name}||{$model->email}";
    }
}
