<?php

namespace App\Http\Controllers;

use App\Services\EnterpriseRegistrationService;
use App\Services\ModuleAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class EnterpriseController extends Controller
{
    protected EnterpriseRegistrationService $registrationService;
    protected ModuleAssignmentService $moduleService;

    public function __construct(
        EnterpriseRegistrationService $registrationService,
        ModuleAssignmentService $moduleService
    )
    {
        $this->registrationService = $registrationService;
        $this->moduleService = $moduleService;
    }


    /**
     * Create new enterprise with admin user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'enterprise_name' => 'required|string|min:2|max:255|unique:enterprises,name',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => ['required', Password::min(8)->mixedCase()->numbers()],
                'confirm_password' => 'required|same:password'
            ]);

            return DB::transaction(function () use ($validated) {
                $result = $this->registrationService->register($validated);
                $this->moduleService->assignCoreModules($result['enterprise_object']);

                unset($result['enterprise_object']);
                
                return response()->json([
                    'message' => __('enterprise.created_successfully'),
                    'data' => $result
                ], 201);
            });
        } catch (ValidationException $e) {
            return response()->json([
                'message' => __('validation.validation_failed'),
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            logger()->error('Enterprise registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'An error occurred during registration',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the authenticated user's enterprise details
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $enterprise = $user->enterprise()->with(['owner'])->first();
            
            if (!$enterprise) {
                return response()->json([
                    'message' => __('enterprise.not_found')
                ], 404);
            }
            
            // Récupérer le nombre d'employés
            $employeeCount = $user->enterprise->users()->count();
            
            return response()->json([
                'message' => __('enterprise.retrieved_successfully'),
                'data' => [
                    'enterprise' => [
                        'uuid' => $enterprise->uuid,
                        'name' => $enterprise->name,
                        'status' => $enterprise->status,
                        'created_at' => $enterprise->created_at,
                        'employee_count' => $employeeCount,
                        'owner' => [
                            'uuid' => $enterprise->owner->uuid,
                            'firstname' => $enterprise->owner->firstname,
                            'lastname' => $enterprise->owner->lastname,
                            'email' => $enterprise->owner->email,
                        ]
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            logger()->error('Failed to retrieve enterprise data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'An error occurred while retrieving enterprise data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}