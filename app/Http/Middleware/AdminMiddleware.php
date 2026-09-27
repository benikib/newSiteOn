<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // ✅ Autoriser les admins et intégrateurs
        if ($user && in_array($user->role, ['admin', 'integrateur'])) {
            return $next($request);
        }

        // ✅ Rediriger les établissements vers leur tableau de bord
        if ($user && $user->role === 'etablissement') {
            return redirect()->route('dashboard_ets'); // ou 'etablissement.dashboard' selon ta route
        }

        // ✅ Bloquer l’accès pour les autres
        abort(403, 'Accès refusé : réservé aux administrateurs et intégrateurs.');
    }
}
