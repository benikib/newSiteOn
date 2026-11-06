<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRegistrationRequest;
use App\Models\TypeEtablissement;
use App\Services\AccountRegistration;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'typesEtablissement' => TypeEtablissement::orderBy('nom')->get(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(AccountRegistrationRequest $request, AccountRegistration $registration): RedirectResponse
    {
        $user = $registration->create($request->validated());

        event(new Registered($user));
        return redirect()->route('login')->with(
            'status',
            'Compte créé. Votre identifiant BISIKA est '.$user->identifiant.'. Le compte sera accessible après validation.'
        );
    }
}
