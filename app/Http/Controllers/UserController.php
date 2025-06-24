<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Service;
use App\Models\TypeEtablissement;
use App\Models\User;
use App\Models\UserEtablissement;
use Illuminate\Http\Request;

class UserController extends Controller
{
     public function index()
    {
        $users = User::all();
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

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'telephone' => $request->telephone,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
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



        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
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
        $etablissement = \App\Models\Etablissement::with('typeEtablissement')->findOrFail($etablissement_id);
        $typesEtablissement =TypeEtablissement::all();
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Etablissement not found.');
        }
        return view('partials.resultatseach', compact('etablissement', 'typesEtablissement'));
    }
    public function ets(Request $request)
{
    $query = $request->input('query');

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

}
