<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class SystemLoggerService
{
    /**
     * Chemin vers le fichier de journal
     * 
     * @var string
     */
    protected $logFile;

    /**
     * Constructeur
     */
    public function __construct()
    {
        // Créer un dossier logs dans storage
        $this->logFile = '/var/www/logs/journal.log';
        
        // S'assurer que le répertoire existe
        $logDir = dirname($this->logFile);
        if (!File::exists($logDir)) {
            File::makeDirectory($logDir, 0755, true);
        }
    }

    /**
     * Journalise un événement d'entreprise
     */
    public function logEnterpriseEvent(string $action, array $data): bool
    {
        $details = json_encode($data);
        return $this->logEvent("ENTERPRISE_{$action}", $details);
    }

    /**
     * Journalise un événement de licence
     */
    public function logLicenseEvent(string $action, array $data): bool
    {
        $details = json_encode($data);
        return $this->logEvent("LICENSE_{$action}", $details);
    }

    /**
     * Journalise un événement de module
     */
    public function logModuleEvent(string $action, array $data): bool
    {
        $details = json_encode($data);
        return $this->logEvent("MODULE_{$action}", $details);
    }

    /**
     * Journalise un événement de rôle
     */
    public function logRoleEvent(string $action, array $data): bool
    {
        $details = json_encode($data);
        return $this->logEvent("ROLE_{$action}", $details);
    }

    /**
     * Journalise une opération sur la base de données
     */
    public function logDbOperation(string $operation, string $table, array $data): bool
    {
        $details = json_encode($data);
        return $this->logEvent("DB_{$operation}_{$table}", $details);
    }

    /**
     * Journalise un événement générique
     */
    public function logEvent(string $type, string $details): bool
    {
        try {
            $timestamp = date('Y-m-d H:i:s');
            $logEntry = "[{$timestamp}] [EVENT] {$type}: {$details}" . PHP_EOL;
            
            // Écrire directement dans le fichier de logs
            File::append($this->logFile, $logEntry);
            
            // Également logger dans Laravel pour avoir une redondance
            Log::info("{$type}: {$details}");
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to log event', [
                'error' => $e->getMessage(),
                'type' => $type,
                'details' => $details
            ]);
            
            return false;
        }
    }
}