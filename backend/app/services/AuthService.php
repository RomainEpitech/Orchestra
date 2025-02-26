<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Authenticate a user and return a token
     *
     * @param array $credentials
     * @return array
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        // Récupérer les données de l'entreprise et du rôle
        $user->load(['enterprise', 'role']);

        // Créer un token
        $token = $user->createToken('api-token');

        return [
            'user' => [
                'uuid' => $user->uuid,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'enterprise' => [
                    'uuid' => $user->enterprise->uuid,
                    'name' => $user->enterprise->name,
                ],
                'role' => [
                    'uuid' => $user->role->uuid,
                    'name' => $user->role->name,
                    'authority' => $user->role->authority,
                    'color_hex' => $user->role->color_hex,
                ],
            ],
            'token' => $token->plainTextToken,
        ];
    }
}