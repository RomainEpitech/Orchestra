<?php

namespace App\Services;

use App\Mail\NewLicenseInvitation;
use App\Models\User;
use App\Exceptions\ModuleLimitExceededException;
use App\Utils\ModuleLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PersonnelLicenseService
{
    /**
     * Create a new license (user) for an enterprise
     *
     * @param array $userData
     * @param string $enterpriseUuid
     * @return array
     * @throws ModuleLimitExceededException
     */
    public function createLicense(array $userData, string $enterpriseUuid): array
    {
        // Obtenir le nombre actuel d'utilisateurs dans l'entreprise
        $currentUserCount = User::where('enterprise_uuid', $enterpriseUuid)->count();
        
        // Vérifier la limite d'utilisateurs
        ModuleLimiter::enforceLimit($enterpriseUuid, 'personnel', 'userLimit', $currentUserCount);
        
        // Générer un mot de passe temporaire
        $temporaryPassword = Str::random(12);
        
        // Créer le nouvel utilisateur
        $user = User::create([
            'firstname' => $userData['firstname'],
            'lastname' => $userData['lastname'],
            'email' => $userData['email'],
            'password' => Hash::make($temporaryPassword),
            'enterprise_uuid' => $enterpriseUuid,
            'role_uuid' => $userData['role_uuid'],
        ]);
        
        // Charger les relations nécessaires
        $user->load(['role', 'enterprise']);
        
        // Envoyer l'email de bienvenue avec les informations de connexion
        $this->sendWelcomeEmail($user, $temporaryPassword);
        
        return [
            'user' => [
                'uuid' => $user->uuid,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'role' => [
                    'uuid' => $user->role->uuid,
                    'name' => $user->role->name,
                    'color_hex' => $user->role->color_hex,
                ],
                'created_at' => $user->created_at,
            ],
            'temporary_password' => $temporaryPassword, // À des fins de debug uniquement
        ];
    }
    
    /**
     * Send welcome email to the new user
     *
     * @param User $user
     * @param string $temporaryPassword
     * @return void
     */
    protected function sendWelcomeEmail(User $user, string $temporaryPassword): void
    {
        try {
            Mail::to($user->email)
                ->send(new NewLicenseInvitation($user, $temporaryPassword));
            
            // Log du succès de l'envoi
            logger()->info('License welcome email sent', [
                'user_email' => $user->email,
                'enterprise_name' => $user->enterprise->name
            ]);
        } catch (\Exception $e) {
            // Log de l'erreur sans bloquer le processus
            logger()->error('Failed to send license welcome email', [
                'user_email' => $user->email,
                'enterprise_name' => $user->enterprise->name,
                'error' => $e->getMessage()
            ]);
        }
    }
}