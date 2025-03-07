<?php

namespace App\Observers;

use App\Models\User;
use App\Services\SystemLoggerService;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Journal log service
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
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        try {
            $user->load(['enterprise', 'role']);
            
            $isOwner = $user->isOwner();
            $eventType = $isOwner ? 'OWNER_CREATED' : 'CREATED';
            
            $this->logger->logLicenseEvent($eventType, [
                'uuid' => $user->uuid,
                'email' => $user->email,
                'fullname' => $user->firstname . ' ' . $user->lastname,
                'enterprise_uuid' => $user->enterprise_uuid,
                'enterprise_name' => $user->enterprise->name ?? 'Unknown',
                'role_uuid' => $user->role_uuid,
                'role_name' => $user->role->name ?? 'Unknown',
                'is_owner' => $isOwner,
                'created_at' => $user->created_at->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log user creation event', [
                'error' => $e->getMessage(),
                'user_uuid' => $user->uuid
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        try {
            $importantAttributes = ['firstname', 'lastname', 'email', 'role_uuid', 'enterprise_uuid'];
            
            if ($user->wasChanged($importantAttributes)) {
                $changedAttributes = array_intersect_key(
                    $user->getChanges(),
                    array_flip($importantAttributes)
                );

                if ($user->wasChanged('role_uuid') && $user->role) {
                    $oldRoleUuid = $user->getOriginal('role_uuid');
                    $newRoleName = $user->role->name;
                    
                    $changedAttributes['role_change'] = [
                        'from_uuid' => $oldRoleUuid,
                        'to_uuid' => $user->role_uuid,
                        'to_name' => $newRoleName
                    ];
                    
                    $this->logger->logRoleEvent('ASSIGNED', [
                        'user_uuid' => $user->uuid,
                        'user_email' => $user->email,
                        'role_uuid' => $user->role_uuid,
                        'role_name' => $newRoleName,
                        'previous_role_uuid' => $oldRoleUuid,
                        'enterprise_uuid' => $user->enterprise_uuid
                    ]);
                }
                
                $this->logger->logLicenseEvent('UPDATED', [
                    'uuid' => $user->uuid,
                    'email' => $user->email,
                    'changed_attributes' => $changedAttributes,
                    'enterprise_uuid' => $user->enterprise_uuid,
                    'updated_at' => $user->updated_at->toIso8601String()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log user update event', [
                'error' => $e->getMessage(),
                'user_uuid' => $user->uuid
            ]);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        try {
            $this->logger->logLicenseEvent('DELETED', [
                'uuid' => $user->uuid,
                'email' => $user->email,
                'fullname' => $user->firstname . ' ' . $user->lastname,
                'enterprise_uuid' => $user->enterprise_uuid,
                'role_uuid' => $user->role_uuid,
                'is_owner' => $user->isOwner(),
                'deleted_at' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log user deletion event', [
                'error' => $e->getMessage(),
                'user_uuid' => $user->uuid
            ]);
        }
    }
}