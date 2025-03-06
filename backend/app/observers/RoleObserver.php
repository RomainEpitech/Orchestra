<?php

namespace App\Observers;

use App\Models\Role;
use App\Services\SystemLoggerService;
use Illuminate\Support\Facades\Log;

class RoleObserver
{
    /**
     * Service de journalisation système
     *
     * @var SystemLoggerService
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param SystemLoggerService $logger
     */
    public function __construct(SystemLoggerService $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Handle the Role "created" event.
     */
    public function created(Role $role): void
    {
        try {
            // Charger l'entreprise associée si nécessaire
            if ($role->enterprise_uuid) {
                $role->load('enterprise');
            }
            
            $this->logger->logRoleEvent('CREATED', [
                'uuid' => $role->uuid,
                'name' => $role->name,
                'enterprise_uuid' => $role->enterprise_uuid,
                'enterprise_name' => $role->enterprise->name ?? null,
                'is_shared' => $role->is_shared,
                'hierarchy_level' => $role->hierarchy_level,
                'created_at' => $role->created_at->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log role creation event', [
                'error' => $e->getMessage(),
                'role_uuid' => $role->uuid
            ]);
        }
    }

    /**
     * Handle the Role "updated" event.
     */
    public function updated(Role $role): void
    {
        try {
            // Vérifier si des attributs importants ont changé
            if ($role->wasChanged(['name', 'authority', 'hierarchy_level'])) {
                // Charger l'entreprise associée si nécessaire
                if ($role->enterprise_uuid) {
                    $role->load('enterprise');
                }
                
                $changedAttributes = array_intersect_key(
                    $role->getChanges(),
                    array_flip(['name', 'authority', 'hierarchy_level'])
                );
                
                // Si l'autorité (permissions) a changé, faire une comparaison détaillée
                if ($role->wasChanged('authority')) {
                    $oldAuthority = json_decode($role->getOriginal('authority'), true) ?? [];
                    $newAuthority = $role->authority ?? [];
                    
                    // Calculer les différences
                    $addedPermissions = [];
                    $removedPermissions = [];
                    
                    // Trouver les permissions ajoutées ou modifiées
                    foreach ($newAuthority as $module => $permissions) {
                        if (!isset($oldAuthority[$module])) {
                            $addedPermissions[$module] = $permissions;
                        } else {
                            foreach ($permissions as $permission => $value) {
                                if (!isset($oldAuthority[$module][$permission]) || 
                                    $oldAuthority[$module][$permission] !== $value) {
                                    $addedPermissions[$module][$permission] = $value;
                                }
                            }
                        }
                    }
                    
                    // Trouver les permissions supprimées
                    foreach ($oldAuthority as $module => $permissions) {
                        if (!isset($newAuthority[$module])) {
                            $removedPermissions[$module] = $permissions;
                        } else {
                            foreach ($permissions as $permission => $value) {
                                if (!isset($newAuthority[$module][$permission])) {
                                    $removedPermissions[$module][$permission] = $value;
                                }
                            }
                        }
                    }
                    
                    $changedAttributes['authority_changes'] = [
                        'added' => $addedPermissions,
                        'removed' => $removedPermissions
                    ];
                }
                
                $this->logger->logRoleEvent('UPDATED', [
                    'uuid' => $role->uuid,
                    'name' => $role->name,
                    'enterprise_uuid' => $role->enterprise_uuid,
                    'enterprise_name' => $role->enterprise->name ?? null,
                    'is_shared' => $role->is_shared,
                    'changed_attributes' => $changedAttributes,
                    'updated_at' => $role->updated_at->toIso8601String()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log role update event', [
                'error' => $e->getMessage(),
                'role_uuid' => $role->uuid
            ]);
        }
    }

    /**
     * Handle the Role "deleted" event.
     */
    public function deleted(Role $role): void
    {
        try {
            // Charger l'entreprise associée si nécessaire
            if ($role->enterprise_uuid) {
                $role->load('enterprise');
            }
            
            $this->logger->logRoleEvent('DELETED', [
                'uuid' => $role->uuid,
                'name' => $role->name,
                'enterprise_uuid' => $role->enterprise_uuid,
                'enterprise_name' => $role->enterprise->name ?? null,
                'is_shared' => $role->is_shared,
                'hierarchy_level' => $role->hierarchy_level,
                'deleted_at' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log role deletion event', [
                'error' => $e->getMessage(),
                'role_uuid' => $role->uuid
            ]);
        }
    }
}