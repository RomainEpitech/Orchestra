<?php

namespace App\Http\Controllers;

use App\Services\EnterpriseRegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class EnterpriseController extends Controller
{
    protected EnterpriseRegistrationService $registrationService;

    public function __construct(EnterpriseRegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
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
                
                return response()->json([
                    'message' => 'Enterprise and admin user created successfully',
                    'data' => $result
                ], 201);
            });
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            // Log l'erreur pour le debugging
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
}