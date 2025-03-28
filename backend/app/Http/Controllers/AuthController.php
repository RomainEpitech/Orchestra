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
                'message' => __('auth.login_successful'),
                'data' => $result
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => __('auth.login_failed'),
                'errors' => $e->errors()
            ], 401);
        } catch (\Exception $e) {
            logger()->error('Login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => __('auth.error_occurred'),
                'error' => config('app.debug') ? $e->getMessage() : __('auth.internal_server_error')
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
                'message' => __('auth.profile_updated'),
                'data' => $result
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => __('auth.profile_update_failed'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            logger()->error('Échec de la mise à jour du profil', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => __('auth.error_occurred'),
                'error' => config('app.debug') ? $e->getMessage() : __('auth.internal_server_error')
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
                    'message' => __('auth.current_password_incorrect'),
                    'errors' => ['current_password' => [__('auth.current_password_incorrect')]]
                ], 422);
            }

            // Passez false pour ne pas révoquer le token actuel
            $this->authService->changePassword($user, $validated['password'], false);

            return response()->json([
                'message' => __('auth.password_changed'),
                'info' => 'Une confirmation a été envoyée par email'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => __('auth.password_changed_failed'),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            logger()->error('Échec du changement de mot de passe', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => __('auth.error_occurred'),
                'error' => config('app.debug') ? $e->getMessage() : __('auth.internal_server_error')
            ], 500);
        }
    }

    /**
     * Logout user and revoke current token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $token = $request->bearerToken();
            
            $success = $this->authService->logout($user, $token);
            
            // Logger l'événement de déconnexion même si le token n'a pas été trouvé
            logger()->info('User logout attempt', [
                'user_uuid' => $user->uuid,
                'user_email' => $user->email,
                'successful' => $success,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'message' => __('auth.logout_successful')
            ]);
        } catch (\Exception $e) {
            logger()->error('Logout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => __('auth.error_occurred'),
                'error' => config('app.debug') ? $e->getMessage() : __('auth.internal_server_error')
            ], 500);
        }
    }
}