<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Login user and create token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $result = $this->authService->login($validated);

            return response()->json([
                'message' => 'Login successful',
                'data' => $result
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Authentication failed',
                'errors' => $e->errors()
            ], 401);
        } catch (\Exception $e) {
            logger()->error('Login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'An error occurred during authentication',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update user profile information
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $rules = [
                'firstname' => 'sometimes|string|max:255',
                'lastname' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $user->id,
                'avatar' => 'sometimes|string',
            ];
            
            $validated = $request->validate($rules);
            
            $result = $this->authService->updateProfile($user, $validated);
            
            return response()->json([
                'message' => 'Profil mis à jour avec succès',
                'data' => $result
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'La mise à jour du profil a échoué',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            logger()->error('Échec de la mise à jour du profil', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour du profil',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Change user password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validate([
                'current_password' => 'required|string',
                'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            ]);
            
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'message' => 'Le mot de passe actuel est incorrect',
                    'errors' => ['current_password' => ['Le mot de passe actuel est incorrect']]
                ], 422);
            }

            // Passez false pour ne pas révoquer le token actuel
            $this->authService->changePassword($user, $validated['password'], false);

            return response()->json([
                'message' => 'Mot de passe modifié avec succès',
                'info' => 'Une confirmation a été envoyée par email'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Le changement de mot de passe a échoué',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            logger()->error('Échec du changement de mot de passe', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Une erreur est survenue lors du changement de mot de passe',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }
}