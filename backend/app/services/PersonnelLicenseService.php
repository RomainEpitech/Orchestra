<?php

namespace App\Services;

use App\Mail\NewLicenseInvitation;
use App\Models\User;
use App\Exceptions\ModuleLimitExceededException;
use App\Models\Enterprise;
use App\Models\Role;
use App\Utils\ModuleLimiter;
use Illuminate\Support\Facades\Hash;
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

    /**
     * Get all licenses (users) for an enterprise
     *
     * @param string $enterpriseUuid
     * @return array
     */
    public function getAllLicenses(string $enterpriseUuid): array
    {
        $users = User::where('enterprise_uuid', $enterpriseUuid)
            ->with('role')
            ->get();
        
        return $users->map(function ($user) {
            return [
                'uuid' => $user->uuid,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'role' => $user->role ? [
                    'uuid' => $user->role->uuid,
                    'name' => $user->role->name,
                    'color_hex' => $user->role->color_hex,
                ] : null,
                'created_at' => $user->created_at->format('Y-m-d'),
            ];
        })->toArray();
    }

    /**
     * Get single licence (user) from enterprise
     * 
     * @param string $userUuid
     * @param string $enterpriseUuid
     * @return array
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getLicence(string $userUuid, string $enterpriseUuid): array
    {
        $user = User::where('uuid', $userUuid)
            ->where('enterprise_uuid', $enterpriseUuid)
            ->with('role')
            ->firstOrFail();
        
        return [
            'uuid' => $user->uuid,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'role' => $user->role ? [
                'uuid' => $user->role->uuid,
                'name' => $user->role->name,
                'color_hex' => $user->role->color_hex,
            ] : null,
            'created_at' => $user->created_at->format('Y-m-d'),
        ];
    }

    /**
     * Update a user license in the enterprise
     *
     * @param string $userUuid
     * @param array $data
     * @param string $adminUuid
     * @param string $enterpriseUuid
     * @return array
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \App\Exceptions\PermissionDeniedException
     */
    public function updateLicense(string $userUuid, array $data, string $adminUuid, string $enterpriseUuid): array
    {
        $admin = User::where('uuid', $adminUuid)
            ->with('role')
            ->firstOrFail();
        
        if (!$admin->role) {
            throw new \App\Exceptions\PermissionDeniedException(
                'You do not have a role assigned'
            );
        }

        $user = User::where('uuid', $userUuid)
            ->where('enterprise_uuid', $enterpriseUuid)
            ->with('role')
            ->firstOrFail();
        
        if ($userUuid === $adminUuid) {
            throw new \App\Exceptions\PermissionDeniedException(
                'You cannot modify your own license as an administrator'
            );
        }
        
        // Vérifier si l'admin a une hiérarchie suffisante pour modifier cet utilisateur
        if ($user->role && !$admin->role->hasHigherOrEqualHierarchyThan($user->role)) {
            throw new \App\Exceptions\PermissionDeniedException(
                'You cannot modify a user with a higher hierarchy level than yours'
            );
        }
        
        // Si mise à jour de rôle, vérifier que l'admin ne peut pas attribuer un rôle supérieur au sien
        if (isset($data['role_uuid']) && $data['role_uuid'] !== $user->role_uuid) {
            $newRole = Role::where('uuid', $data['role_uuid'])->firstOrFail();
            if (!$admin->role->hasHigherOrEqualHierarchyThan($newRole)) {
                throw new \App\Exceptions\PermissionDeniedException(
                    'You cannot assign a role with a higher hierarchy level than yours'
                );
            }
        }
        
        $originalData = $user->toArray();
        $user->update($data);
        $user = $user->fresh(['role', 'enterprise']);
        
        $changes = [];
        foreach ($data as $key => $value) {
            if (isset($originalData[$key]) && $originalData[$key] !== $value) {
                $changes[$key] = [
                    'from' => $originalData[$key],
                    'to' => $value
                ];
            }
        }
        
        $loggerService = app(SystemLoggerService::class);
        $loggerService->logLicenseEvent('UPDATED', [
            'uuid' => $user->uuid,
            'email' => $user->email,
            'fullname' => $user->firstname . ' ' . $user->lastname,
            'enterprise_uuid' => $user->enterprise_uuid,
            'enterprise_name' => $user->enterprise->name ?? 'Unknown',
            'role_uuid' => $user->role_uuid,
            'role_name' => $user->role->name ?? 'Unknown',
            'admin_uuid' => $adminUuid,
            'admin_email' => $admin->email ?? 'Unknown',
            'admin_name' => $admin->firstname . ' ' . $admin->lastname,
            'changed_attributes' => $changes,
            'request_ip' => request()->ip(),
            'request_user_agent' => request()->userAgent(),
            'updated_at' => now()->toIso8601String()
        ]);
        
        logger()->info('User license updated', [
            'user_uuid' => $userUuid,
            'admin_uuid' => $adminUuid,
            'enterprise_uuid' => $enterpriseUuid,
            'updated_fields' => array_keys($data)
        ]);
        
        return [
            'user' => [
                'uuid' => $user->uuid,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'role_uuid' => $user->role_uuid,
                'role_name' => $user->role->name ?? null,
                'avatar' => $user->avatar,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ]
        ];
    }
}