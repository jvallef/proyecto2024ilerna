<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/');
        }

        $activeRole = session('active_role', $user->roles->first()->name ?? null);
        if (!$activeRole) {
            return redirect('/')->with('error', 'No role assigned');
        }

        return match($activeRole) {
            'admin' => redirect('/admin'),
            'teacher' => redirect('/teacher'),
            'student' => redirect('/student'),
            default => redirect('/')
        };
    }

    /**
     * Muestra el dashboard de administrador
     */
    public function admin()
    {
        $usersCount = User::count();
        $areasCount = Area::count();
        $reportsCount = 0;
        return view('admin.dashboard', compact('usersCount', 'areasCount', 'reportsCount'));
    }

    /**
     * Muestra el dashboard de profesor
     */
    public function teacher()
    {
        return view('teacher.dashboard');
    }

    /**
     * Muestra el dashboard de estudiante
     */
    public function student()
    {
        return view('student.dashboard');
    }
}
