<?php

namespace App\Http\Controllers;

use App\Mail\UserCreatedMail;
use App\Models\Etablissement;
use App\Models\Publicite;
use App\Models\Service;
use App\Models\TypeEtablissement;
use App\Models\User;
use App\Models\UserEtablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
     public function admins()
    {
        $users = User::where('role', 'admin')->get();
        return view('admins.users.index',compact("users"));

    }
      public function index()
    {
        $users = User::where('role', '!=', 'admin')->get();
        return view('admins.users.index',compact("users"));

    }
      public function users_ets($etablissement)
    {
        $users = UserEtablissement::where('etablissement_id', $etablissement)
            ->with('user')
            ->get()
            ->pluck('user');

        if ($users->isEmpty()) {
            return redirect()->back()->with('error', 'No users found for this etablissement.');
        }

        return view('admins.users_etablissements.index',compact("users","etablissement"));

    }
public function repportingAdmins()
{
     $taux = \App\Models\TauxDeChange::all();

    $users = User::all();
    // Données pour le graphique des inscriptions
    $registrationDates = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai'];
    $registrationCounts = [10, 20, 15, 25, 30];

    // Données pour le graphique des établissements
    $etablissementTypes = \App\Models\TypeEtablissement::all();
    $etablissementCounts = [15, 10, 8];
    $etablissements = \App\Models\Etablissement::with('typeEtablissement')->get();
    $publicites = \App\Models\Publicite::with('etablissement')->get();
    $typeEtablissements = \App\Models\TypeEtablissement::all();

    return view('admins.index', compact('users', 'registrationDates', 'registrationCounts', 'etablissementTypes', 'etablissementCounts', 'etablissements', 'publicites','typeEtablissements'));
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

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'telephone' => 'required',
            'role' => 'required|in:admin,client,etablissement'
        ]);

     $user  = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'telephone' => $request->telephone,
        ]);

        $token = Password::createToken($user);
        $url = url("/reset-password/{$token}?email={$user->email}"); // Store the plain password for email
         Mail::to(["benikasu7@gmail.com",$user->email])->send(new UserCreatedMail($user, $url));

        return redirect()->back()->with('success', 'User created successfully.');
    }
     public function store_ets(Request $request)
    { try {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'telephone' => 'required',
            'role' => 'required|in:admin,client,etablissement',
            'etablissement_id' => 'required|exists:etablissements,id'
        ]);

       $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'telephone' => $request->telephone,
        ]);
        // Associate the user with the etablissement
        UserEtablissement::create([
            'user_id' => $user->id,
            'etablissement_id' => $request->etablissement_id,
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */

    public function update(Request $request,  $id)
    {
        try {
            $user = User::findOrFail($id);


        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'nullable|min:6',
            'telephone' => 'required',
            'role' => 'required|in:admin,client,etablissement'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->telephone = $request->telephone;
        $user->role = $request->role;



        $user->save(); // juste sauvegarder
$token = Password::createToken($user); // utiliser l’objet User
$url = url("/reset-password/{$token}?email={$user->email}");

Mail::to(["benikasu7@gmail.com", $user->email])
    ->send(new UserCreatedMail($user, $url));


        return redirect()->back()->with('success', 'User updated successfully.');
        } catch (\Exception $e) {

            dd($e->getMessage());
            return redirect()->route('users.index')->with('error', 'User not found.');
        }
    }


    public function update_ets(Request $request,  $id)
    {
        try {
            $user = User::findOrFail($id);


        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'nullable|min:6',
            'telephone' => 'required',

        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->telephone = $request->telephone;



        $user->save();

        return redirect()->back()->with('success', 'User updated successfully.');
        } catch (\Exception $e) {


            return redirect()->route('users.index')->with('error', 'User not found.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
    public function destroy_ets( $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }
    public function show(User $user)
    {
        return view('admins.users.show', compact('user'));
    }
    public function ets_info($etablissement_id)
    {
         $photos = $publicitesActives = Publicite::actives()
        ->orderBy('id', 'desc')
        ->get();
     $etablissement = Etablissement::with('typeEtablissement')->findOrFail($etablissement_id);
        $typesEtablissement =TypeEtablissement::all();
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Etablissement not found.');
        }
        return view('partials.resultatseach', compact('etablissement', 'typesEtablissement','photos'));
    }
    public function ets(Request $request)
{

    $photos = $publicitesActives = Publicite::actives()
        ->orderBy('id', 'desc')
        ->get();

    // Recherche dans les établissements
    $etablissements = Etablissement::where('nom', 'like', "%{$query}%")
        ->orWhere('description', 'like', "%{$query}%")
        ->get();

    // Recherche dans les services
    $services = Service::where('nom', 'like', "%{$query}%")
        ->orWhere('description', 'like', "%{$query}%")
        ->get();

    return view('partials.resultat', compact('etablissements', 'services', 'query'));
}

public function search(Request $request)
{
    $query = $request->input('query');
    $typeEtablissement = $request->input('type_etablissement');
    $ville = $request->input('ville');
    $commune = $request->input('commune');
    $quartier = $request->input('quartier');
    $noteMin = $request->input('note_min');

    $budgetMax = $request->input('budget_max');
    // $hasPublicite = $request->input('has_publicite');
    // $hasPromotion = $request->input('has_promotion');
    $hasPromotion = $request->boolean('has_promotion');
    $hasPublicite = $request->boolean('has_publicite');

    $results = Etablissement::query()
        ->when($query, function($q) use ($query) {
            $q->where(function($q) use ($query) {
                $q->where('nom', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('services', function($q) use ($query) {
                      $q->where('nom', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                  });
            });
        })
        ->when($typeEtablissement, function($q) use ($typeEtablissement) {
            $q->whereHas('typeEtablissement', function($q) use ($typeEtablissement) {
                $q->where('nom', $typeEtablissement);
            });
        })
        ->when($ville, function($q) use ($ville) {
            $q->where('ville', 'like', "%{$ville}%");
        })
        ->when($commune, function($q) use ($commune) {
            $q->where('commune', 'like', "%{$commune}%");
        })
      ->when($budgetMax, function($q) use ($budgetMax) {
    $q->whereHas('services', function($subQuery) use ($budgetMax) {
        $subQuery->where('prix', '<=', $budgetMax);
    });
})

        ->when($quartier, function($q) use ($quartier) {
            $q->where('quartier', 'like', "%{$quartier}%");
        })
        // ->when($noteMin, function($q) use ($noteMin) {
        //     $q->where('note_moyenne', '>=', $noteMin);
        // })
        ->when($hasPromotion, function($q) {
            $q->whereHas('promotions', function($q) {
                $q->where('Date_fin', '>=', now());
            });
        })
        // ->when($hasPublicite, function($q) {
        //     $q->whereHas('publicites', function($q) {
        //         $q->where('status', 'actif');
        //     });
        // })
        ->with(['typeEtablissement', 'services', 'photos', 'promotions', 'publicites'])

        ->paginate(10);

    return view('partials.resultat', [
        'results' => $results,
        'filters' => $request->all()
    ]);
}
public function delete($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return redirect()->route('users.index')->with('success', 'User deleted successfully.');}

}
