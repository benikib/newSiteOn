<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EtablissementMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */  public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && $user->role === 'etablissement') {
            return $next($request);
        }

        abort(403, 'Accès refusé : réservé aux administrateurs des etablissments.');
    }

}
