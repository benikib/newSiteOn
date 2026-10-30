<?php

namespace App\Http\Middleware;

use App\Models\Etablissement;
use App\Services\AbonnementService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MobileEstablishmentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $etablissement = Etablissement::query()
            ->where('statut', 'actif')
            ->whereHas('users', fn ($query) => $query->where('users.id', $user->id))
            ->find($request->route('etablissement'));

        if (! $etablissement) {
            return response()->json([
                'message' => 'Établissement introuvable ou accès non autorisé.',
            ], 404);
        }

        if ($user->role !== 'etablissement'
            || ! app(AbonnementService::class)->refreshEtablissement($etablissement)) {
            return response()->json([
                'message' => 'Un abonnement valide est requis pour accéder à cet établissement.',
            ], 403);
        }

        return $next($request);
    }
}
