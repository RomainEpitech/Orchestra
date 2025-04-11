<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Authenticate a user and return a token
     *
     * @param array $credentials
     * @return array
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Incorrect credentials.'],
            ]);
        }

        $user->load(['enterprise', 'role']);
        $token = $user->createToken('api-token');

        return [
            'user' => [
                'uuid' => $user->uuid,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'enterprise' => [
                    'uuid' => $user->enterprise->uuid,
                    'name' => $user->enterprise->name,
                ],
                'role' => [
                    'uuid' => $user->role->uuid,
                    'name' => $user->role->name,
                    'authority' => $user->role->authority,
                    'hierarchy' => $user->role->hierarchy_level,
                    'color_hex' => $user->role->color_hex,
                ],
            ],
            'token' => $token->plainTextToken,
        ];
    }

    /**
     * Update user profile
     *
     * @param User $user
     * @param array $data
     * @return array
     */
    public function updateProfile($user, array $data): array
    {
        $user->update($data);
        $user = $user->fresh();
        
        return [
            'user' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'enterprise_uuid' => $user->enterprise_uuid,
                'role_uuid' => $user->role_uuid,
            ]
        ];
    }

    /**
     * Change user password
     *
     * @param User $user
     * @param string $password
     * @param bool $shouldRevokeTokens
     * @return void
     */
    public function changePassword(User $user, string $password, bool $shouldRevokeTokens = true): void
    {
        $user->update([
            'password' => Hash::make($password)
        ]);
        
        $request = request();
        $ipAddress = $request ? $request->ip() : null;
        $userAgent = $request ? $request->userAgent() : null;
        $user->tokens()->delete();
        
        \App\Jobs\ProcessPasswordChange::dispatch(
            $user, 
            $shouldRevokeTokens,
            $ipAddress,
            $userAgent
        );
    }

    /**
     * Logout user by revoking current token
     *
     * @param User $user
     * @param string|null $token
     * @return bool
     */
    public function logout(User $user, ?string $token = null): bool
    {
        try {
            if (!$token) {
                logger()->warning('Logout attempted without token', [
                    'user_uuid' => $user->uuid,
                    'user_email' => $user->email
                ]);
                return false;
            }

            $tokenId = $user->currentAccessToken()->id;
            $user->tokens()->where('id', $tokenId)->delete();
            
            logger()->info('Token successfully revoked', [
                'user_uuid' => $user->uuid,
                'token_id' => $tokenId
            ]);
            
            return true;
        } catch (\Exception $e) {
            logger()->error('Logout service failed', [
                'user_uuid' => $user->uuid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
}