<?php

namespace App\Http\Controllers;

use App\Mail\UserCreatedMail;
use App\Models\Etablissement;
use App\Models\Photo;
use App\Models\Paiement;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Promotion;
use App\Models\TypeEtablissement;
use App\Models\User;
use App\Models\UserEtablissement;
use App\Models\Service;
use App\Models\Publicite;
use App\Services\AbonnementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class EtablissementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Etablissement::withCount(['services', 'promotions', 'publicites', 'photos'])
            ->with(['typeEtablissement', 'services', 'promotions'])
            ->orderBy('created_at', 'desc');
        if ($request->boolean('abonnements_migration')) {
            $query->whereHas('abonnements', function ($abonnements) {
                $abonnements->where('type_operation', 'migration');
            });
        }
        $etablissements = $query->paginate(10)->withQueryString();
        $typeEtablissements = TypeEtablissement::all();


        return view('admins.etablissements.index', compact('etablissements', 'typeEtablissements'));
    }
    public function users_ets()
    {
        try {

       $userEtablissements = UserEtablissement::with('user','etablissement')
            ->where('etablissement_id', Auth::user()->usersEtablissements->first()?->etablissement_id)
            ->get();



            return view('etablissements.users_etablissements.index', compact('userEtablissements'));

        }
        catch (\Exception $e) {
            report($e);
            return redirect()->back()->withErrors(['error' => 'Impossible de charger les utilisateurs de cet établissement. Réessayez.']);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {


            $request->validate([
                'nom' => 'required|string|max:100',
                'ville' => 'required|string|max:100',
                'commune' => 'required|string|max:100',
                'avenue' => 'nullable|string|max:100',
                'quartier' => 'nullable|string|max:100',
                'numero' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'telephone' => 'nullable|string|max:20',
                'type_etablissement_id' => 'required|exists:type_etablissements,id',
                'user_name' => 'nuLlable|string|max:100',
                'user_email' => 'nullable|email|max:100|unique:users,email',
                'user_password' => 'nullable|string|min:8',
                'user_role' => 'nullable',
            ]);


            if (in_array(auth()->user()->role, ['admin', 'integrateur'])) {

                $user = User::create([
                    'name' => $request->user_name,
                    'email' => $request->user_email,
                    'password' => Hash::make($request->user_password),
                    'role' => $request->user_role,
                ]);
                $etablissements = Etablissement::create($request->all());
                UserEtablissement::create([
                    'user_id' => $user->id,
                    'etablissement_id' => $etablissements->id,
                ]);
        $token = Password::createToken($user);
        $url = url("/reset-password/{$token}?email={$user->email}"); // Store the plain password for email
        Mail::to($user->email)->send(new UserCreatedMail($user, $url));
            } elseif (Auth::user()->role === 'etablissement') {

                $etablissements = Etablissement::create($request->all());
                UserEtablissement::create([
                    'user_id' => Auth::user()->id,
                    'etablissement_id' => $etablissements->id,
                ]);

            } else {

                return redirect()->back()->withErrors(['error' => 'Vous n\'êtes pas autorisé à créer un établissement.']);
            }
            return redirect()->back()->with('success', 'Établissement créé avec succès.');


        } catch (\Illuminate\Validation\ValidationException $e) {

                
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( $etablissement)
    {
           $etablissement = \App\Models\Etablissement::with('typeEtablissement')->findOrFail($etablissement);
            $typesEtablissement =TypeEtablissement::all();
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Établissement introuvable.');
        }

    return view('admins.etablissements.show', compact('etablissement', 'typesEtablissement'));
    }
     public function showOne( $etablissement)
    {
           $etablissementRecord = Etablissement::with('typeEtablissement')
                ->where('slug', $etablissement)
                ->first();

            if (!$etablissementRecord && is_numeric($etablissement)) {
                $etablissementRecord = Etablissement::with('typeEtablissement')->findOrFail($etablissement);
            }
            abort_unless($etablissementRecord, 404);
            $etablissement = $etablissementRecord;
            $typesEtablissement =TypeEtablissement::all();

    return view('etablissements.partials.show', compact('etablissement', 'typesEtablissement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Etablissement $etablissement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $etablissement = Etablissement::findOrFail($id);

            $request->validate([
                'nom' => 'required|string|max:100',
                'ville' => 'required|string|max:100',
                'commune' => 'required|string|max:100',
                'avenue' => 'nullable|string|max:100',
                'quartier' => 'nullable|string|max:100',
                'numero' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'telephone' => 'nullable|string|max:20',
                'type_etablissement_id' => 'required|exists:type_etablissements,id',

            ]);

            $etablissemen = $etablissement->update($request->all());


            return redirect()->back()->with('success', 'Établissement mis à jour avec succès.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
public function note_moyenne(Request $request, Etablissement $etablissement)
    {

        $request->validate([
            'note_moyenne' => 'required|numeric|min:1|max:5',
        ]);

        try {
            $etablissement->update([

                'note_moyenne' => $request->note_moyenne,
            ]);

            return redirect()->back()->with('success', 'Note ajoutée avec succès.');
        } catch (\Exception $e) {
            report($e);
            return redirect()->back()->withErrors(['error' => 'Impossible d’enregistrer cette note. Réessayez.']);
        }
    }


    public function updateStatut(Request $request, Etablissement $etablissement, AbonnementService $abonnementService)
    {

        $request->validate([
            'statut' => 'required|in:en_attente,actif,desactive'
        ]);

        if ($request->statut === 'actif') {
            if (!$abonnementService->refreshEtablissement($etablissement)) {
                return redirect()->back()->withErrors([
                    'statut' => 'Ajoutez un nouvel abonnement dans l’historique pour réactiver cet établissement.',
                ]);
            }
        } elseif ($request->statut === 'desactive') {
            $data = $request->validate(['motif' => 'required|string|max:2000']);
            $abonnementService->suspend($etablissement, $data['motif'], $request->user());
        } else {
            if ($abonnementService->currentPeriod($etablissement)) {
                return redirect()->back()->withErrors([
                    'statut' => 'Suspendez l’abonnement depuis son historique avant de placer cet établissement en attente.',
                ]);
            }
            $etablissement->update(['statut' => 'en_attente']);
        }

        return redirect()->back()->with('success', 'Statut de l’établissement mis à jour.');
    }
     public function destroy(Etablissement $etablissement)
    {
        try {
            $etablissement->delete();
            return redirect()->back()->with('success', 'Établissement supprimé avec succès.');
        } catch (\Exception $e) {
            report($e);
            return redirect()->back()->withErrors(['error' => 'Impossible de supprimer cet établissement. Réessayez.']);
        }
    }
    // EtablissementController.phpuse App\Models\Etablissement;

public function dashboard()
{
    $userId = auth()->id();
    $now = now()->toImmutable();
    $etablissementIds = Etablissement::whereHas('users', function ($query) use ($userId) {
        $query->where('users.id', $userId);
    })->pluck('id');

    // Statistiques de base
    $stats = [
        'promotions_actives' => Service::whereNotNull('promotion')
            ->where('promotion', '>', 0)
            ->where('date_debut_promo', '<=', $now)
            ->where('date_fin_promo', '>=', $now)
            ->whereHas('etablissement', function($query) use ($userId) {
                $query->whereHas('users', function($q) use ($userId) {
                    $q->where('users.id', $userId);
                });
            })->count(),

        'total_services' => Service::whereHas('etablissement', function($query) use ($userId) {
            $query->whereHas('users', function($q) use ($userId) {
                $q->where('users.id', $userId);
            });
        })->count(),

        'total_publicites' => Publicite::whereHas('etablissement.users', function($query) use ($userId) {
            $query->where('users.id', $userId);
        })->count(),

        'total_paiements' => Paiement::whereHas('service.etablissement.users', function($query) use ($userId) {
            $query->where('users.id', $userId);
        })->sum('montant'),

        'paiements_mois_courant' => Paiement::whereHas('service.etablissement.users', function($query) use ($userId) {
            $query->where('users.id', $userId);
        })
        ->whereYear('created_at', $now->year)
        ->whereMonth('created_at', $now->month)
        ->sum('montant'),

        'ventes_jour' => Order::whereIn('etablissement_id', $etablissementIds)
            ->where('type', 'pos')
            ->whereDate('order_date', $now->toDateString())
            ->sum('total_amount'),

        'nombre_ventes_jour' => Order::whereIn('etablissement_id', $etablissementIds)
            ->where('type', 'pos')
            ->whereDate('order_date', $now->toDateString())
            ->count(),

        'stock_faible' => Stock::whereIn('etablissement_id', $etablissementIds)
            ->whereRaw('quantity <= minimum_stock')
            ->where('quantity', '>', 0)
            ->count(),

        'stock_epuise' => Stock::whereIn('etablissement_id', $etablissementIds)
            ->where('quantity', '<=', 0)
            ->count(),
    ];

    // Données pour le graphique d'évolution mensuelle (6 derniers mois)
    $monthlyData = [
        'labels' => [],
        'paiements' => [],
        'services' => [],
        'publicites' => []
    ];

    for ($i = 5; $i >= 0; $i--) {
        $date = $now->subMonths($i);
        $monthYear = $date->translatedFormat('M Y');

        $monthlyData['labels'][] = $monthYear;

        $monthlyData['paiements'][] = Paiement::whereHas('service.etablissement.users', function($query) use ($userId) {
            $query->where('users.id', $userId);
        })
        ->whereYear('created_at', $date->year)
        ->whereMonth('created_at', $date->month)
        ->sum('montant');

        $monthlyData['services'][] = Service::whereHas('etablissement.users', function($q) use ($userId) {
            $q->where('users.id', $userId);
        })
        ->whereYear('created_at', $date->year)
        ->whereMonth('created_at', $date->month)
        ->count();

        $monthlyData['publicites'][] = Publicite::whereHas('etablissement.users', function($q) use ($userId) {
            $q->where('users.id', $userId);
        })
        ->whereYear('created_at', $date->year)
        ->whereMonth('created_at', $date->month)
        ->count();
    }

    // Récupération des paiements par service (remplace $categories)
    $paiementsParService = Paiement::whereHas('service.etablissement.users', function($query) use ($userId) {
        $query->where('users.id', $userId);
    })
    ->join('services', 'services.id', '=', 'paiements.service_id')
    ->select('services.nom as service_nom', DB::raw('SUM(paiements.montant) as total'))
    ->groupBy('services.nom')
    ->pluck('total', 'service_nom');

    return view('etablissements.dashboard', [
        'stats' => $stats,
        'monthlyData' => $monthlyData,
        'paiementsParService' => $paiementsParService,
        'etablissements' => Etablissement::whereIn('id', $etablissementIds)->get(['id', 'nom']),
        'selectedEtablissementId' => $etablissementIds->first(),
    ]);
}
    public function updatetitre(Request $request, Etablissement $etablissement)
    {

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type_etablissement_id' => 'required|exists:type_etablissements,id'
        ]);

        $etablissement->update($validated);

        return back()->with('success', 'Profil mis à jour avec succès');
    }

    public function updatePhoto(Request $request, Etablissement $etablissement)
    {
        $request->validate([
            'photo' => 'required|image|max:2048'
        ]);

        // Supprimer l'ancienne photo si elle existe
        if ($etablissement->photos->isNotEmpty()) {
            // Ici vous devriez implémenter la suppression du fichier physique
            $etablissement->photos()->delete();
        }

        // Enregistrer la nouvelle photo
        $path = $request->file('photo')->store('photos', 'public');

        $etablissement->photos()->create([
            'image_path' => $path,
            'titre' => 'Photo de profil'
        ]);


        return back()->with('success', 'Photo de profil mise à jour');
    }

    public function updateContact(Request $request, Etablissement $etablissement)
    {
        $user = $request->user();
        $isAdministrator = $user && in_array($user->role, ['admin', 'integrateur'], true);
        $isAssignedManager = $user && $user->role === 'etablissement'
            && DB::table('user_etablissements')
                ->where('user_id', $user->id)
                ->where('etablissement_id', $etablissement->id)
                ->exists();

        abort_unless($isAdministrator || $isAssignedManager, 403);

        $whatsappNumber = preg_replace('/[\s()+.\-]/', '', (string) $request->input('whatsapp_number', ''));
        if (str_starts_with($whatsappNumber, '0')) {
            $whatsappNumber = '243' . substr($whatsappNumber, 1);
        }
        $request->merge(['whatsapp_number' => $whatsappNumber ?: null]);

        $validated = $request->validate([
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'whatsapp_number' => ['nullable', 'regex:/^[1-9][0-9]{7,14}$/'],
            'whatsapp_message' => 'nullable|string|max:500',
        ]);

        $etablissement->update($validated);

        return back()->with('success', 'Contacts mis à jour');
    }

    public function updateDescription(Request $request, Etablissement $etablissement)
    {
        $validated = $request->validate([
            'description' => 'nullable|string'
        ]);

        $etablissement->update($validated);

        return back()->with('success', 'Description mise à jour');
    }

    public function updateAddress(Request $request, Etablissement $etablissement)
    {
        $validated = $request->validate([
            'ville' => 'required|string|max:255',
            'commune' => 'required|string|max:255',
            'avenue' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'itineraire' => 'nullable|url|max:2048',
        ]);

        $etablissement->update($validated);

        return back()->with('success', 'Adresse mise à jour');
    }

    public function updateSocial(Request $request, Etablissement $etablissement)
    {
        $validated = $request->validate([
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255'
        ]);

        $etablissement->update($validated);

        return back()->with('success', 'Réseaux sociaux mis à jour');
    }
}

