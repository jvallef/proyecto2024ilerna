<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class RoleSwitchController extends Controller
{
    public function switch($role): RedirectResponse
    {
        $user = auth()->user();
        
        // Verificar si el usuario tiene el rol solicitado
        if (!$user->hasRole($role)) {
            return back()->with('error', 'No tienes permiso para cambiar a este rol.');
        }

        // Almacenar el rol activo en la sesión
        session(['active_role' => $role]);

        // Redirigir al dashboard correspondiente
        return match($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('workarea.dashboard'),
            'student' => redirect()->route('classroom.dashboard'),
            default => redirect('/')
        };
    }
}
