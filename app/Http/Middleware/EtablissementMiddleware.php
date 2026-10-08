<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AbonnementService;
use Symfony\Component\HttpFoundation\Response;

class EtablissementMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
    */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if ($user && $user->role === 'etablissement') {
            if (!app(AbonnementService::class)->userHasValidSubscription($user)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Votre abonnement est expiré ou inactif. Contactez l’administrateur de BISIKA.',
                ]);
            }

            return $next($request);
        }

        abort(403, 'Accès refusé : réservé aux administrateurs des etablissments.');
    }

}
