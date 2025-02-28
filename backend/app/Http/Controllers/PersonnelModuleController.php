<?php

namespace App\Http\Controllers;

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
}