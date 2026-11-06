<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class AccountRegistrationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'type_compte' => ['nullable', 'in:client,entite_formalisee,entite_non_formalisee,organisation_patronale'],
            'telephone' => ['required_if:type_compte,entite_formalisee,entite_non_formalisee,organisation_patronale', 'nullable', 'string', 'max:25'],
            'nom_entite' => ['required_if:type_compte,entite_formalisee,entite_non_formalisee,organisation_patronale', 'nullable', 'string', 'max:255'],
            'type_etablissement_id' => ['required_if:type_compte,entite_formalisee,entite_non_formalisee', 'nullable', 'exists:type_etablissements,id'],
            'ville' => ['nullable', 'string', 'max:255'],
            'commune' => ['nullable', 'string', 'max:255'],
            'quartier' => ['nullable', 'string', 'max:255'],
            'avenue' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:100'],
        ];
    }
}
