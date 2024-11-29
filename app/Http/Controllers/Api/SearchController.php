<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

abstract class SearchController extends Controller
{
    /**
     * Get the model class to search.
     */
    abstract protected function getModelClass(): string;

    /**
     * Get the fields to search in.
     */
    abstract protected function getSearchFields(): array;

    /**
     * Format the suggestion for display.
     */
    abstract protected function formatSuggestion($model): string;

    /**
     * Get additional query constraints.
     */
    protected function additionalConstraints($query)
    {
        return $query;
    }

    /**
     * Get suggestions based on search query.
     */
    public function suggestions(Request $request)
    {
        try {
            $searchTerm = $request->get('q', '');
            
            Log::info('Search query received', [
                'model' => $this->getModelClass(),
                'query' => $searchTerm
            ]);
            
            if (strlen($searchTerm) < 2) {
                return response()->json([]);
            }

            $modelClass = $this->getModelClass();
            $query = $modelClass::query();

            // Apply search conditions for each field
            $query->where(function($q) use ($searchTerm) {
                $first = true;
                foreach ($this->getSearchFields() as $field) {
                    if ($first) {
                        $q->where($field, 'LIKE', "%{$searchTerm}%");
                        $first = false;
                    } else {
                        $q->orWhere($field, 'LIKE', "%{$searchTerm}%");
                    }
                }
            });

            // Apply any additional constraints
            $query = $this->additionalConstraints($query);

            $suggestions = $query->limit(10)
                ->get()
                ->map(fn($model) => $this->formatSuggestion($model))
                ->values()
                ->toArray();

            Log::info('Search results', [
                'model' => $this->getModelClass(),
                'count' => count($suggestions)
            ]);
            
            return response()->json($suggestions);
        } catch (\Exception $e) {
            Log::error('Error in search', [
                'model' => $this->getModelClass(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Error processing search'], 500);
        }
    }
}
