<?php

namespace App\Services;

use App\Models\Enterprise;
use App\Models\User;
use App\Services\KeyGeneratorService;
use Illuminate\Support\Facades\Hash;

class EnterpriseRegistrationService
{
    protected KeyGeneratorService $keyGenerator;

    public function __construct(KeyGeneratorService $keyGenerator)
    {
        $this->keyGenerator = $keyGenerator;
    }

    public function register(array $data): array
    {
        // Générer une clé unique
        $enterpriseKey = $this->keyGenerator->generateUniqueKey();

        // Créer l'entreprise
        $enterprise = Enterprise::create([
            'name' => $data['enterprise_name'],
            'key' => $enterpriseKey,
            'status' => true,
        ]);

        // Créer l'utilisateur propriétaire
        $user = User::create([
            'firstname' => $data['first_name'],
            'lastname' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'enterprise_uuid' => $enterprise->uuid,
        ]);

        // Mettre à jour l'entreprise avec l'ID du propriétaire
        $enterprise->owner_uuid = $user->uuid;
        $enterprise->save();

        // Retourner les ressources créées
        return [
            'enterprise' => $enterprise->only(['uuid', 'name', 'key', 'status']),
            'owner' => $user->only(['uuid', 'firstname', 'lastname', 'email'])
        ];
    }
}