<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        
        // Almacenar el rol activo en la sesión si no existe
        if ($request->session()->missing('active_role')) {
            $firstRole = $request->user()->roles->first();
            $request->session()->put('active_role', $firstRole->name);
        }
        
        $activeRole = $request->session()->get('active_role');
        
        // Redirigir basado en el rol activo
        return match($activeRole) {
            'admin' => redirect('/admin'),
            'teacher' => redirect('/workarea'),
            'student' => redirect('/classroom'),
            default => redirect('/dashboard')
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
