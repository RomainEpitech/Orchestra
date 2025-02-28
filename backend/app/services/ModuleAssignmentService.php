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
        // Récupérer tous les modules core
        $coreModules = Module::where('is_core', true)->get();
        
        foreach ($coreModules as $module) {
            // Créer une entrée dans enterprise_modules
            EnterpriseModule::create([
                'uuid' => Str::uuid()->toString(),
                'enterprise_uuid' => $enterprise->uuid,
                'module_uuid' => $module->uuid,
                'status' => 'active',
                // Par défaut, les modules sont en version gratuite
                // à moins que le module n'ait pas de limites définies
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
        // Trouver l'entrée existante
        $enterpriseModule = EnterpriseModule::where('enterprise_uuid', $enterprise->uuid)
            ->where('module_uuid', $module->uuid)
            ->first();
        
        if ($enterpriseModule) {
            // Passer en version premium
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
        // Trouver le module par sa clé
        $module = Module::where('key', $moduleKey)->first();
        
        if (!$module) {
            return false; // Module inexistant
        }
        
        // Vérifier si l'entreprise a ce module
        $enterpriseModule = EnterpriseModule::where('enterprise_uuid', $enterprise->uuid)
            ->where('module_uuid', $module->uuid)
            ->where('status', 'active')
            ->first();
        
        if (!$enterpriseModule) {
            return false; // Module non attribué à cette entreprise
        }
        
        // Si version premium, aucune limite à vérifier
        if ($enterpriseModule->is_premium) {
            return true;
        }
        
        // Sinon, vérifier les limites de la version gratuite
        $limits = json_decode($module->free_limits, true);
        
        if (!isset($limits[$limitKey])) {
            return true; // Pas de limite définie pour cette fonctionnalité
        }
        
        // Vérifier si la valeur demandée est inférieure ou égale à la limite
        return $requestedValue <= $limits[$limitKey];
    }
}