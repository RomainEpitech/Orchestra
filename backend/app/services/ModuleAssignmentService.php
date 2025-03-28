<?php

namespace App\Services;

use App\Models\Enterprise;
use App\Models\Module;
use App\Models\EnterpriseModule;
use Illuminate\Support\Str;

class ModuleAssignmentService
{
    /**
     * Assigne tous les modules core à une entreprise nouvellement créée
     *
     * @param Enterprise $enterprise
     * @return void
     */
    public function assignCoreModules(Enterprise $enterprise): void
    {
        $coreModules = Module::where('is_core', true)->get();
        
        foreach ($coreModules as $module) {
            EnterpriseModule::create([
                'uuid' => Str::uuid()->toString(),
                'enterprise_uuid' => $enterprise->uuid,
                'module_uuid' => $module->uuid,
                'status' => 'active',
                'is_premium' => ($module->free_limits === null),
            ]);
        }
    }

    /**
     * Passe un module d'une entreprise en version premium
     *
     * @param Enterprise $enterprise
     * @param Module $module
     * @return bool
     */
    public function upgradeToPremium(Enterprise $enterprise, Module $module): bool
    {
        $enterpriseModule = EnterpriseModule::where('enterprise_uuid', $enterprise->uuid)
            ->where('module_uuid', $module->uuid)
            ->first();
        
        if ($enterpriseModule) {
            $enterpriseModule->update([
                'is_premium' => true
            ]);
            
            return true;
        }
        
        return false;
    }

    /**
     * Vérifie si une entreprise peut effectuer une action selon les limites de son module
     *
     * @param Enterprise $enterprise
     * @param string $moduleKey
     * @param string $limitKey
     * @param mixed $requestedValue
     * @return bool
     */
    public function checkModuleLimit(Enterprise $enterprise, string $moduleKey, string $limitKey, $requestedValue): bool
    {
        $module = Module::where('key', $moduleKey)->first();
        
        if (!$module) {
            return false;
        }
        
        $enterpriseModule = EnterpriseModule::where('enterprise_uuid', $enterprise->uuid)
            ->where('module_uuid', $module->uuid)
            ->where('status', 'active')
            ->first();
        
        if (!$enterpriseModule) {
            return false;
        }
        
        if ($enterpriseModule->is_premium) {
            return true;
        }
        
        $limits = json_decode($module->free_limits, true);
        
        if (!isset($limits[$limitKey])) {
            return true;
        }
        
        return $requestedValue <= $limits[$limitKey];
    }
}