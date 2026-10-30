<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    private function userData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'telephone' => $user->telephone,
        ];
    }
}
