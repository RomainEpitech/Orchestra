<?php

namespace App\Exceptions;

use Exception;

class ModuleLimitExceededException extends Exception
{
    protected int $currentCount;
    protected int $limit;

    /**
     * Construire l'exception
     * 
     * @param int $currentCount Le nombre actuel d'éléments
     * @param int $limit La limite maximale autorisée
     * @param string $message Le message d'erreur
     */
    public function __construct(
        int $currentCount,
        int $limit,
        string $message = 'Module limit reached'
    ) {
        $this->currentCount = $currentCount;
        $this->limit = $limit;
        
        parent::__construct($message);
    }

    /**
     * Obtenir le nombre actuel d'éléments
     * 
     * @return int
     */
    public function getCurrentCount(): int 
    {
        return $this->currentCount;
    }

    /**
     * Obtenir la limite maximale
     * 
     * @return int
     */
    public function getLimit(): int 
    {
        return $this->limit;
    }
}