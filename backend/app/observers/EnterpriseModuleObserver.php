<?php

namespace App\Observers;

use App\Models\EnterpriseModule;
use App\Services\SystemLoggerService;
use Illuminate\Support\Facades\Log;

class EnterpriseModuleObserver
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
     * Handle the EnterpriseModule "created" event.
     */
    public function created(EnterpriseModule $enterpriseModule): void
    {
        try {
            // Charger les relations nécessaires
            $enterpriseModule->load(['enterprise', 'module']);
            
            $statusText = $enterpriseModule->is_premium ? 'premium' : 'free';
            $actionType = $enterpriseModule->is_premium ? 'PREMIUM_ACTIVATED' : 'ACTIVATED';
            
            $this->logger->logModuleEvent($actionType, [
                'uuid' => $enterpriseModule->uuid,
                'enterprise_uuid' => $enterpriseModule->enterprise_uuid,
                'enterprise_name' => $enterpriseModule->enterprise->name ?? 'Unknown',
                'module_uuid' => $enterpriseModule->module_uuid,
                'module_name' => $enterpriseModule->module->name ?? 'Unknown',
                'module_key' => $enterpriseModule->module->key ?? 'unknown',
                'status' => $enterpriseModule->status,
                'is_premium' => $enterpriseModule->is_premium,
                'type' => $statusText,
                'created_at' => $enterpriseModule->created_at->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log module activation event', [
                'error' => $e->getMessage(),
                'enterprise_module_uuid' => $enterpriseModule->uuid
            ]);
        }
    }

    /**
     * Handle the EnterpriseModule "updated" event.
     */
    public function updated(EnterpriseModule $enterpriseModule): void
    {
        try {
            // Détecter un passage de gratuit à premium
            if (!$enterpriseModule->getOriginal('is_premium') && $enterpriseModule->is_premium) {
                $enterpriseModule->load(['enterprise', 'module']);
                
                $this->logger->logModuleEvent('UPGRADED', [
                    'uuid' => $enterpriseModule->uuid,
                    'enterprise_uuid' => $enterpriseModule->enterprise_uuid,
                    'enterprise_name' => $enterpriseModule->enterprise->name ?? 'Unknown',
                    'module_uuid' => $enterpriseModule->module_uuid,
                    'module_name' => $enterpriseModule->module->name ?? 'Unknown',
                    'module_key' => $enterpriseModule->module->key ?? 'unknown',
                    'previous_status' => $enterpriseModule->getOriginal('status'),
                    'new_status' => $enterpriseModule->status,
                    'updated_at' => $enterpriseModule->updated_at->toIso8601String()
                ]);
            }
            // Changement de statut (activation/désactivation)
            elseif ($enterpriseModule->wasChanged('status')) {
                $enterpriseModule->load(['enterprise', 'module']);
                
                $actionType = $enterpriseModule->status === 'active' ? 'ACTIVATED' : 'DEACTIVATED';
                
                $this->logger->logModuleEvent($actionType, [
                    'uuid' => $enterpriseModule->uuid,
                    'enterprise_uuid' => $enterpriseModule->enterprise_uuid,
                    'enterprise_name' => $enterpriseModule->enterprise->name ?? 'Unknown',
                    'module_uuid' => $enterpriseModule->module_uuid,
                    'module_name' => $enterpriseModule->module->name ?? 'Unknown',
                    'module_key' => $enterpriseModule->module->key ?? 'unknown',
                    'previous_status' => $enterpriseModule->getOriginal('status'),
                    'is_premium' => $enterpriseModule->is_premium,
                    'updated_at' => $enterpriseModule->updated_at->toIso8601String()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log module update event', [
                'error' => $e->getMessage(),
                'enterprise_module_uuid' => $enterpriseModule->uuid
            ]);
        }
    }

    /**
     * Handle the EnterpriseModule "deleted" event.
     */
    public function deleted(EnterpriseModule $enterpriseModule): void
    {
        try {
            $enterpriseModule->load(['enterprise', 'module']);
            
            $this->logger->logModuleEvent('DELETED', [
                'uuid' => $enterpriseModule->uuid,
                'enterprise_uuid' => $enterpriseModule->enterprise_uuid,
                'enterprise_name' => $enterpriseModule->enterprise->name ?? 'Unknown',
                'module_uuid' => $enterpriseModule->module_uuid,
                'module_name' => $enterpriseModule->module->name ?? 'Unknown',
                'module_key' => $enterpriseModule->module->key ?? 'unknown',
                'status' => $enterpriseModule->status,
                'is_premium' => $enterpriseModule->is_premium,
                'deleted_at' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log module deletion event', [
                'error' => $e->getMessage(),
                'enterprise_module_uuid' => $enterpriseModule->uuid
            ]);
        }
    }
}