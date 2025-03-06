<?php

namespace App\Observers;

use App\Models\Enterprise;
use App\Services\SystemLoggerService;
use Illuminate\Support\Facades\Log;

class EnterpriseObserver
{
    /**
     * Service de journalisation système
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
     * Handle the Enterprise "created" event.
     */
    public function created(Enterprise $enterprise): void
    {
        try {
            $this->logger->logEnterpriseEvent('CREATED', [
                'uuid' => $enterprise->uuid,
                'name' => $enterprise->name,
                'owner_uuid' => $enterprise->owner_uuid,
                'created_at' => $enterprise->created_at->toIso8601String()
            ]);

            // Également logger dans Laravel pour avoir une redondance
            Log::info('Enterprise created', [
                'enterprise_uuid' => $enterprise->uuid,
                'enterprise_name' => $enterprise->name
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log enterprise creation event', [
                'error' => $e->getMessage(),
                'enterprise_uuid' => $enterprise->uuid
            ]);
        }
    }

    /**
     * Handle the Enterprise "updated" event.
     */
    public function updated(Enterprise $enterprise): void
    {
        try {
            // Ne logger que si des attributs importants ont changé
            if ($enterprise->isDirty(['name', 'status', 'owner_uuid'])) {
                $changedAttributes = array_intersect_key(
                    $enterprise->getDirty(),
                    array_flip(['name', 'status', 'owner_uuid'])
                );
                
                $this->logger->logEnterpriseEvent('UPDATED', [
                    'uuid' => $enterprise->uuid,
                    'changed_attributes' => $changedAttributes,
                    'updated_at' => $enterprise->updated_at->toIso8601String()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log enterprise update event', [
                'error' => $e->getMessage(),
                'enterprise_uuid' => $enterprise->uuid
            ]);
        }
    }

    /**
     * Handle the Enterprise "deleted" event.
     */
    public function deleted(Enterprise $enterprise): void
    {
        try {
            $this->logger->logEnterpriseEvent('DELETED', [
                'uuid' => $enterprise->uuid,
                'name' => $enterprise->name,
                'deleted_at' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log enterprise deletion event', [
                'error' => $e->getMessage(),
                'enterprise_uuid' => $enterprise->uuid
            ]);
        }
    }

    /**
     * Handle the Enterprise "restored" event.
     */
    public function restored(Enterprise $enterprise): void
    {
        try {
            $this->logger->logEnterpriseEvent('RESTORED', [
                'uuid' => $enterprise->uuid,
                'name' => $enterprise->name,
                'restored_at' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log enterprise restoration event', [
                'error' => $e->getMessage(),
                'enterprise_uuid' => $enterprise->uuid
            ]);
        }
    }
}