<?php

namespace App\Http\Controllers;

use App\Exceptions\ModuleLimitExceededException;
use App\Models\Module;
use App\Models\User;
use App\Services\PersonnelLicenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PersonnelModuleController extends Controller
{
    protected PersonnelLicenseService $licenseService;

    public function __construct(PersonnelLicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Create a new user license for the enterprise
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createLicense(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'role_uuid' => 'required|exists:roles,uuid',
            ]);

            return DB::transaction(function () use ($request, $validated) {
                $result = $this->licenseService->createLicense(
                    $validated,
                    $request->user()->enterprise_uuid
                );
                
                return response()->json([
                    'message' => 'New license created successfully',
                    'data' => $result
                ], 201);
            });
        } catch (ModuleLimitExceededException $e) {
            // Récupérer les informations du module personnel
            $personnelModule = Module::where('key', 'personnel')->first();
            
            return response()->json([
                'message' => 'User limit reached',
                'error' => [
                    'type' => 'module_limit_exceeded',
                    'module' => 'personnel',
                    'current_count' => $e->getCurrentCount(),
                    'limit' => $e->getLimit(),
                    'upgrade_info' => [
                        'module_name' => $personnelModule ? $personnelModule->name : 'Personnel',
                        'price' => $personnelModule ? $personnelModule->price : null
                    ]
                ]
            ], 403); // 403 Forbidden est approprié pour les limitations
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            logger()->error('License creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'An error occurred during license creation',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
    * Delete a user license from the enterprise
    *
    * @param Request $request
    * @param string $userUuid
    * @return JsonResponse
    */
    public function deleteLicense(Request $request, string $userUuid): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request, $userUuid) {
                $adminUuid = $request->user()->uuid;
                $enterpriseUuid = $request->user()->enterprise_uuid;
                
                $result = $this->licenseService->deleteLicense(
                    $userUuid,
                    $adminUuid,
                    $enterpriseUuid
                );
                
                return response()->json([
                    'message' => 'License deleted successfully',
                    'data' => $result
                ]);
            });
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found or not part of your enterprise',
            ], 404);
        } catch (\App\Exceptions\PermissionDeniedException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        } catch (\Exception $e) {
            logger()->error('License deletion failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_uuid' => $userUuid
            ]);
            
            return response()->json([
                'message' => 'An error occurred during license deletion',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get all enterprise collaborators licenses
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllLicenses(Request $request): JsonResponse
    {
        // Get the enterprise UUID from the authenticated user
        $enterpriseUuid = $request->user()->enterprise_uuid;
        
        // Use the service to get all licenses for this enterprise
        $personnelLicenseService = app(PersonnelLicenseService::class);
        $licenses = $personnelLicenseService->getAllLicenses($enterpriseUuid);
        
        return response()->json([
            'success' => true,
            'licenses' => $licenses
        ]);
    }

    public function getUserLicense(Request $request, string $userUuid): JsonResponse
    {
        $enterpriseUuid = $request->user()->enterprise_uuid;
        
        $personnelLicenseService = app(PersonnelLicenseService::class);
        
        try {
            $license = $personnelLicenseService->getLicence($userUuid, $enterpriseUuid);
            
            return response()->json([
                'success' => true,
                'license' => $license
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'License not found'
            ], 404);
        }
    }
}