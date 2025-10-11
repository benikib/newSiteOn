<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $request->session()->regenerate();

    // Vérifie si l'utilisateur est admin
    if (auth()->user()->role === 'etablissement') {
         $id = auth()->user()->usersEtablissements();

        return redirect()->route('users.etablissements'); // Remplace par ta vraie route
    }


   if (in_array(auth()->user()->role,['admin', 'integrateur'] )) {
         $id = auth()->user()->usersEtablissements();

        return redirect()->route('dashboard'); // Remplace par ta vraie route
    }
    return redirect()->route('/');
}


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
