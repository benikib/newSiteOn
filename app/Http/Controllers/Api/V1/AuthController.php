<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRegistrationRequest;
use App\Models\Etablissement;
use App\Models\User;
use App\Services\AccountRegistration;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Les identifiants fournis sont incorrects.',
            ]);
        }

        abort_unless($user->is_approved, 403, 'Votre compte est en attente de validation.');

        $issuedToken = $user->createToken($validated['device_name'], ['*'], now()->addDays(30));

        return response()->json([
            'data' => [
                'token' => $issuedToken->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $issuedToken->accessToken->expires_at,
                'user' => $this->userData($user),
            ],
        ]);
    }

    public function register(AccountRegistrationRequest $request, AccountRegistration $registration): JsonResponse
    {
        $user = $registration->create($request->validated());
        event(new Registered($user));

        return response()->json([
            'data' => [
                'user' => $this->userData($user),
                'message' => 'Compte créé. Il sera accessible après validation par un administrateur.',
            ],
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();
        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $establishments = Etablissement::query()
            ->whereHas('users', fn ($query) => $query->where('users.id', $user->id))
            ->get(['id', 'nom', 'statut']);

        return response()->json([
            'data' => [
                'user' => $this->userData($user),
                'establishments' => $establishments->map(fn (Etablissement $item) => [
                    'id' => $item->id,
                    'name' => $item->nom,
                    'status' => $item->statut,
                ]),
            ],
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'telephone' => ['sometimes', 'nullable', 'string', 'max:25'],
        ]);
        $user->update($data);

        return response()->json(['data' => ['user' => $this->userData($user->fresh())]]);
    }

    private function userData(User $user): array
    {
        $missingFields = [];
        if (! $user->telephone) {
            $missingFields[] = 'telephone';
        }
        if ($user->role === 'etablissement') {
            $establishments = $user->etablissements()->get();
            if ($establishments->isEmpty()) {
                $missingFields[] = 'etablissement';
            }
            foreach ($establishments as $establishment) {
                foreach ([
                    'nom' => 'etablissement.nom',
                    'type_etablissement_id' => 'etablissement.domaine',
                    'ville' => 'etablissement.ville',
                    'commune' => 'etablissement.commune',
                    'telephone' => 'etablissement.telephone',
                ] as $field => $label) {
                    if (! $establishment->{$field}) {
                        $missingFields[] = $label;
                    }
                }
                if ($establishment->nature_entite === 'formalisee') {
                    foreach (['rccm', 'id_nat', 'cnss', 'inpp', 'nif', 'autorisation_path'] as $field) {
                        if (! $establishment->{$field}) {
                            $missingFields[] = 'etablissement.'.$field;
                        }
                    }
                }
            }
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'telephone' => $user->telephone,
            'identifiant' => $user->identifiant,
            'is_approved' => $user->is_approved,
            'can_publish' => $user->can_publish,
            'missing_fields' => array_values(array_unique($missingFields)),
        ];
    }
}
