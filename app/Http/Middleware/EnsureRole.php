<?php

namespace App\Http\Middleware;

use App\Helpers\UserHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Illuminate\Support\Facades\Log;

class EnsureRole
{
    /**
     * Gestiona una petición entrante y comprueba si tiene el rol de profesor
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|RedirectResponse)  $next
     * @param  string  $role el role requerido
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $role = null): Response|RedirectResponse
    {
        $requiredRoute = $request->route()->getName();

        try {
            if (Auth::check() && Auth::user()->hasRole($role)) {
                $response = $next($request);

                // ojo hay que procesar la respuesta para que coincida con lo que debe devolver
                if (!($response instanceof Response || $response instanceof RedirectResponse)){
                    $response = response()->make($response, 200) ;
                }

                return $response;
            }

            UserHelper::logUserData($request, $request->path(), $role, $requiredRoute);

            // Aquí aseguramos que siempre se devuelve un Response o RedirectResponse
            return response()->make(['message' => 'No tienes autorización para acceder a esta página.'], 403);

        } catch (\Exception $e) {
            Log::error('Error en EnsureTeacher middleware: ' . $e->getMessage());
            return response('Error interno del servidor', 500);
        }
    }
}
