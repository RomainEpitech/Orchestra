<?php

namespace App\Utils;

use App\Exceptions\ModuleLimitExceededException;
use App\Models\Enterprise;
use App\Models\EnterpriseModule;
use App\Models\Module;

class ModuleLimiter
{
    /**
     * Vérifie si une action respecte les limites d'un module pour une entreprise
     *
     * @param string $enterpriseUuid L'UUID de l'entreprise
     * @param string $moduleKey La clé du module
     * @param string $limitKey La clé de la limite à vérifier
     * @param int $requestedValue La valeur à comparer à la limite
     * @return bool True si la limite est respectée, false sinon
     */
    public static function checkLimit(string $enterpriseUuid, string $moduleKey, string $limitKey, int $requestedValue): bool
    {
        // Trouver le module par sa clé
        $module = Module::where('key', $moduleKey)->first();
        
        if (!$module) {
            return false; // Module inexistant
        }
        
        // Vérifier si l'entreprise a ce module
        $enterpriseModule = EnterpriseModule::where('enterprise_uuid', $enterpriseUuid)
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
        $limits = $module->free_limits;
        
        // Si free_limits est null ou ne contient pas la limite demandée
        if ($limits === null || !isset($limits[$limitKey])) {
            return true; // Pas de limite définie pour cette fonctionnalité
        }
        
        // Vérifier si la valeur demandée est inférieure ou égale à la limite
        return $requestedValue <= $limits[$limitKey];
    }
    
    /**
     * Vérifie et lance une exception si la limite est dépassée
     *
     * @param string $enterpriseUuid
     * @param string $moduleKey
     * @param string $limitKey
     * @param int $currentCount
     * @throws ModuleLimitExceededException
     */
    public static function enforceLimit(string $enterpriseUuid, string $moduleKey, string $limitKey, int $currentCount): void
    {
        $module = Module::where('key', $moduleKey)->first();
        
        if (!$module) {
            throw new \RuntimeException("Module {$moduleKey} not found");
        }
        
        $limits = $module->free_limits;
        $limit = $limits[$limitKey] ?? PHP_INT_MAX;
        
        if (!self::checkLimit($enterpriseUuid, $moduleKey, $limitKey, $currentCount + 1)) {
            throw new ModuleLimitExceededException($currentCount, $limit);
        }
    }
}