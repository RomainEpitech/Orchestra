<?php

namespace App\Services;

use App\Mail\NewLicenseInvitation;
use App\Models\User;
use App\Exceptions\ModuleLimitExceededException;
use App\Models\Enterprise;
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
        \App\Jobs\SendLicenseEmail::dispatch($user, $temporaryPassword);
    }

    /**
     * Delete a user license from an enterprise
     *
     * @param string $userUuid UUID of the user to delete
     * @param string $adminUuid UUID of the admin performing the deletion
     * @param string $enterpriseUuid UUID of the enterprise
     * @return array
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \App\Exceptions\PermissionDeniedException
     */
    public function deleteLicense(string $userUuid, string $adminUuid, string $enterpriseUuid): array
    {
        // Empêcher de supprimer son propre compte
        if ($userUuid === $adminUuid) {
            throw new \App\Exceptions\PermissionDeniedException("You cannot delete your own account");
        }
        
        // Récupérer l'utilisateur à supprimer et vérifier qu'il appartient à la bonne entreprise
        $userToDelete = User::where('uuid', $userUuid)
            ->where('enterprise_uuid', $enterpriseUuid)
            ->with('role')
            ->firstOrFail();
        
        // Récupérer l'administrateur qui effectue la suppression
        $admin = User::where('uuid', $adminUuid)
            ->with(['role', 'enterprise'])
            ->firstOrFail();
        
        // Récupérer l'entreprise pour vérifier qui est le propriétaire
        $enterprise = Enterprise::where('uuid', $enterpriseUuid)
            ->first();
            
        $isOwner = $enterprise && $enterprise->owner_uuid === $admin->uuid;
        
        // Si l'admin n'est pas le propriétaire, vérifier la hiérarchie des rôles
        if (!$isOwner) {
            // Vérifier si l'utilisateur à supprimer a un rôle de niveau strictement inférieur
            if (!$admin->role || !$userToDelete->role || 
                $admin->role->hierarchy_level >= $userToDelete->role->hierarchy_level) {
                throw new \App\Exceptions\PermissionDeniedException(
                    "You can only delete users with a lower hierarchy level than yours"
                );
            }
        }
        
        // Récupérer des informations sur l'utilisateur avant de le supprimer
        $userData = [
            'uuid' => $userToDelete->uuid,
            'firstname' => $userToDelete->firstname,
            'lastname' => $userToDelete->lastname,
            'email' => $userToDelete->email,
            'role' => $userToDelete->role ? [
                'uuid' => $userToDelete->role->uuid,
                'name' => $userToDelete->role->name
            ] : null,
            'deleted_at' => now()
        ];
        
        // Enregistrer l'action dans les logs pour le système de traçabilité futur
        logger()->info('User license deleted', [
            'deleted_user_uuid' => $userUuid,
            'admin_uuid' => $adminUuid,
            'enterprise_uuid' => $enterpriseUuid,
            'is_owner' => $isOwner,
            'timestamp' => now()->toIso8601String()
        ]);
        
        // Supprimer l'utilisateur
        $userToDelete->delete();
        
        return [
            'deleted_user' => $userData,
            'deleted_by' => $adminUuid,
            'deleted_by_owner' => $isOwner,
            'enterprise_uuid' => $enterpriseUuid
        ];
    }
}