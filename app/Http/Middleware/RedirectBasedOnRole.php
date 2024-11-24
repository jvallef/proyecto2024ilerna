<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            if ($request->user()->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }
            if ($request->user()->hasRole('teacher')) {
                return redirect()->route('teacher.dashboard');
            }
            if ($request->user()->hasRole('student')) {
                return redirect()->route('student.dashboard');
            }
        }

        return $next($request);
    }
}
