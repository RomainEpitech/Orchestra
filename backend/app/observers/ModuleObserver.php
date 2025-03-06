<?php

namespace App\Observers;

use App\Models\Module;
use App\Services\SystemLoggerService;
use Illuminate\Support\Facades\Log;

class ModuleObserver
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
     * Handle the Module "created" event.
     */
    public function created(Module $module): void
    {
        try {
            $this->logger->logModuleEvent('CREATED', [
                'uuid' => $module->uuid,
                'name' => $module->name,
                'key' => $module->key,
                'is_core' => $module->is_core,
                'free_limits' => $module->free_limits,
                'price' => $module->price,
                'created_at' => $module->created_at->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log module creation event', [
                'error' => $e->getMessage(),
                'module_uuid' => $module->uuid
            ]);
        }
    }

    /**
     * Handle the Module "updated" event.
     */
    public function updated(Module $module): void
    {
        try {
            // Vérifier si des attributs importants ont changé
            $importantAttributes = ['name', 'description', 'free_limits', 'price', 'is_core'];
            
            if ($module->wasChanged($importantAttributes)) {
                $changedAttributes = array_intersect_key(
                    $module->getChanges(),
                    array_flip($importantAttributes)
                );
                
                // Si les limites gratuites ont changé, faire une comparaison détaillée
                if ($module->wasChanged('free_limits')) {
                    $oldLimits = json_decode($module->getOriginal('free_limits'), true) ?? [];
                    $newLimits = $module->free_limits ?? [];
                    
                    $changedAttributes['free_limits_changes'] = [
                        'before' => $oldLimits,
                        'after' => $newLimits
                    ];
                }
                
                // Si le prix a changé, enregistrer l'ancien et le nouveau prix
                if ($module->wasChanged('price')) {
                    $changedAttributes['price_changes'] = [
                        'before' => $module->getOriginal('price'),
                        'after' => $module->price
                    ];
                }
                
                $this->logger->logModuleEvent('UPDATED', [
                    'uuid' => $module->uuid,
                    'name' => $module->name,
                    'key' => $module->key,
                    'changed_attributes' => $changedAttributes,
                    'updated_at' => $module->updated_at->toIso8601String()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log module update event', [
                'error' => $e->getMessage(),
                'module_uuid' => $module->uuid
            ]);
        }
    }

    /**
     * Handle the Module "deleted" event.
     */
    public function deleted(Module $module): void
    {
        try {
            $this->logger->logModuleEvent('DELETED', [
                'uuid' => $module->uuid,
                'name' => $module->name,
                'key' => $module->key,
                'is_core' => $module->is_core,
                'deleted_at' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log module deletion event', [
                'error' => $e->getMessage(),
                'module_uuid' => $module->uuid
            ]);
        }
    }
}