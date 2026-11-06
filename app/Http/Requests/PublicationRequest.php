<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['categorie' => $this->input('categorie', 'News')]);
    }

    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:200'],
            'synopsis' => ['nullable', 'string', 'max:500'],
            'contenu' => ['required', 'string', 'max:20000'],
            'categorie' => ['required', 'in:Vulgarisation,Rappel,News'],
            'cible' => ['required', 'in:tous,domaines'],
            'types_etablissement' => ['required_if:cible,domaines', 'array'],
            'types_etablissement.*' => ['integer', 'distinct', 'exists:type_etablissements,id'],
            'ville' => ['required_with:commune', 'nullable', 'string', 'max:255'],
            'commune' => ['nullable', 'string', 'max:255'],
            'publie_le' => ['nullable', 'date'],
            'expire_le' => ['nullable', 'date', 'after_or_equal:publie_le'],
        ];
    }
}
