<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolesModuleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Récupère tous les rôles avec un niveau hiérarchique inférieur à celui de l'utilisateur connecté
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAssignableRoles(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $roles = $this->roleService->getRolesWithLowerHierarchy($user->uuid);
            
            return response()->json([
                'message' => 'Assignable roles retrieved successfully',
                'data' => $roles->map(function ($role) {
                    return [
                        'uuid' => $role->uuid,
                        'name' => $role->name,
                        'color_hex' => $role->color_hex,
                        'hierarchy_level' => $role->hierarchy_level,
                        'is_shared' => $role->is_shared,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            logger()->error('Failed to retrieve assignable roles', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'An error occurred while retrieving assignable roles',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Récupère tous les rôles disponibles pour l'entreprise de l'utilisateur connecté
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllRoles(Request $request): JsonResponse
    {
        try {
            $enterpriseUuid = $request->user()->enterprise_uuid;
            $roles = $this->roleService->getAllEnterpriseRoles($enterpriseUuid);
            
            return response()->json([
                'message' => 'All roles retrieved successfully',
                'data' => $roles->map(function ($role) {
                    return [
                        'uuid' => $role->uuid,
                        'name' => $role->name,
                        'color_hex' => $role->color_hex,
                        'hierarchy_level' => $role->hierarchy_level,
                        'is_shared' => $role->is_shared,
                        'authority' => $role->authority,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            logger()->error('Failed to retrieve all roles', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'An error occurred while retrieving all roles',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}