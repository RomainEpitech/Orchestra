<?php

namespace App\Services;

use App\Mail\EnterpriseCreatedMail;
use App\Models\Enterprise;
use App\Models\User;
use App\Services\KeyGeneratorService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EnterpriseRegistrationService
{
    protected KeyGeneratorService $keyGenerator;

    public function __construct(KeyGeneratorService $keyGenerator)
    {
        $this->keyGenerator = $keyGenerator;
    }

    /**
     * Register a new enterprise with an owner
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        // Generate a unique key for the enterprise
        $enterpriseKey = $this->keyGenerator->generateUniqueKey();

        // Create the enterprise
        $enterprise = Enterprise::create([
            'name' => $data['enterprise_name'],
            'key' => $enterpriseKey,
            'status' => true,
        ]);

        // Create the owner user
        $user = User::create([
            'firstname' => $data['first_name'],
            'lastname' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'enterprise_uuid' => $enterprise->uuid,
        ]);

        // Update enterprise with owner
        $enterprise->owner_uuid = $user->uuid;
        $enterprise->save();

        // Send welcome email with enterprise details
        $this->sendWelcomeEmail($enterprise, $user);

        // Return the created resources (sanitized)
        return [
            'enterprise' => [
                'uuid' => $enterprise->uuid,
                'name' => $enterprise->name,
                'status' => $enterprise->status,
            ],
            'owner' => [
                'uuid' => $user->uuid,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
            ]
        ];
    }

    /**
     * Send welcome email to the enterprise owner
     *
     * @param Enterprise $enterprise
     * @param User $owner
     * @return void
     */
    protected function sendWelcomeEmail(Enterprise $enterprise, User $owner): void
    {
        try {
            Mail::to($owner->email)
                ->send(new EnterpriseCreatedMail(
                    enterprise: $enterprise,
                    owner: $owner,
                    recoveryKey: $enterprise->key
                ));
            
            // Enregistrement de l'envoi réussi
            logger()->info('Enterprise welcome email sent', [
                'enterprise_uuid' => $enterprise->uuid,
                'owner_email' => $owner->email
            ]);
        } catch (\Exception $e) {
            // Enregistrement de l'erreur, mais ne pas bloquer le processus
            logger()->error('Failed to send enterprise welcome email', [
                'enterprise_uuid' => $enterprise->uuid,
                'owner_email' => $owner->email,
                'error' => $e->getMessage()
            ]);
        }
    }
}