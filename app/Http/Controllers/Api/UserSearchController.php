<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserSearchController extends Controller
{
    public function suggestions(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            Log::info('Search query received', ['query' => $query]);
            
            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $suggestions = User::where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->select('name', 'email')
                ->limit(10)
                ->get()
                ->map(function ($user) {
                    return "{$user->name} ({$user->email})";
                })
                ->values()
                ->toArray();

            Log::info('Search results', ['count' => count($suggestions)]);
            
            return response()->json($suggestions);
        } catch (\Exception $e) {
            Log::error('Error in user search', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Error processing search'], 500);
        }
    }
}
