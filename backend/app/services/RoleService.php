<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
    /**
     * Récupère tous les rôles ayant un niveau de hiérarchie inférieur à celui de l'utilisateur spécifié
     * 
     * @param string $userUuid UUID de l'utilisateur
     * @param string|null $enterpriseUuid UUID de l'entreprise (optionnel)
     * @return Collection|array Collection de rôles ou tableau vide
     */
    public function getRolesWithLowerHierarchy(string $userUuid, ?string $enterpriseUuid = null): Collection|array
    {
        $user = User::where('uuid', $userUuid)
            ->with('role')
            ->first();
            
        if (!$user || !$user->role) {
            return collect([]);
        }
        
        if (!$enterpriseUuid) {
            $enterpriseUuid = $user->enterprise_uuid;
        }
        
        $userHierarchyLevel = $user->role->hierarchy_level;
        return Role::where(function ($query) use ($enterpriseUuid) {
                $query->where('enterprise_uuid', $enterpriseUuid)
                    ->orWhere('is_shared', true);
            })
            ->where('hierarchy_level', '>', $userHierarchyLevel)
            ->orderBy('hierarchy_level', 'asc')
            ->get();
    }
    
    /**
     * Récupère tous les rôles disponibles pour une entreprise
     * 
     * @param string $enterpriseUuid UUID de l'entreprise
     * @return Collection Collection de rôles
     */
    public function getAllEnterpriseRoles(string $enterpriseUuid): Collection
    {
        return Role::where('enterprise_uuid', $enterpriseUuid)
            ->orWhere('is_shared', true)
            ->orderBy('hierarchy_level', 'asc')
            ->get();
    }
}