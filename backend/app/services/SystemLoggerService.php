<?php

namespace App\Services;

use App\Jobs\LogSystemEvent;
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
     * Couleurs ANSI pour le terminal
     * 
     * @var array
     */
    protected $colors = [
        'reset' => "\033[0m",
        'bold' => "\033[1m",
        'dim' => "\033[2m",
        'black' => "\033[30m",
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'magenta' => "\033[35m",
        'cyan' => "\033[36m",
        'white' => "\033[37m",
        'bg_green' => "\033[42m",
        'bg_yellow' => "\033[43m",
        'bg_blue' => "\033[44m",
        'bg_magenta' => "\033[45m",
    ];

    /**
     * Couleurs par type d'événement
     * 
     * @var array
     */
    protected $eventColors = [
        'ENTERPRISE_' => 'blue',
        'LICENSE_' => 'green',
        'MODULE_' => 'magenta',
        'ROLE_' => 'cyan',
        'DB_' => 'yellow',
    ];

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
        $details = json_encode($data, JSON_PRETTY_PRINT);
        // Dispatch le job au lieu de l'exécuter directement
        LogSystemEvent::dispatch("ENTERPRISE_{$action}", $details);
        return true;
    }

    /**
     * Journalise un événement de licence
     */
    public function logLicenseEvent(string $action, array $data): bool
    {
        $details = json_encode($data, JSON_PRETTY_PRINT);
        LogSystemEvent::dispatch("LICENSE_{$action}", $details);
        return true;
    }

    /**
     * Journalise un événement de module
     */
    public function logModuleEvent(string $action, array $data): bool
    {
        $details = json_encode($data, JSON_PRETTY_PRINT);
        LogSystemEvent::dispatch("MODULE_{$action}", $details);
        return true;
    }

    /**
     * Journalise un événement de rôle
     */
    public function logRoleEvent(string $action, array $data): bool
    {
        $details = json_encode($data, JSON_PRETTY_PRINT);
        LogSystemEvent::dispatch("ROLE_{$action}", $details);
        return true;
    }

    /**
     * Journalise une opération sur la base de données
     */
    public function logDbOperation(string $operation, string $table, array $data): bool
    {
        $details = json_encode($data, JSON_PRETTY_PRINT);
        LogSystemEvent::dispatch("DB_{$operation}_{$table}", $details);
        return true;
    }

    /**
     * Détermine la couleur pour un type d'événement
     */
    protected function getColorForEventType(string $type): string
    {
        foreach ($this->eventColors as $prefix => $color) {
            if (strpos($type, $prefix) === 0) {
                return $this->colors[$color];
            }
        }
        return $this->colors['reset'];
    }

    /**
     * Format les détails JSON pour une meilleure lisibilité
     */
    protected function formatJson(string $json): string
    {
        $data = json_decode($json, true);
        if (!$data) return $json;
        
        // Extraire les informations principales
        $mainInfo = [];
        
        // Informations clés à extraire selon le contexte
        if (isset($data['name'])) $mainInfo[] = "name: " . $data['name'];
        if (isset($data['fullname'])) $mainInfo[] = "fullname: " . $data['fullname'];
        if (isset($data['email'])) $mainInfo[] = "email: " . $data['email'];
        if (isset($data['module_name'])) $mainInfo[] = "module: " . $data['module_name'];
        if (isset($data['enterprise_name'])) $mainInfo[] = "enterprise: " . $data['enterprise_name'];
        if (isset($data['role_name'])) $mainInfo[] = "role: " . $data['role_name'];

        // Extraire les changements principaux
        $changes = [];
        if (isset($data['changed_attributes'])) {
            foreach ($data['changed_attributes'] as $key => $value) {
                $changes[] = "$key → $value";
            }
        }
        
        // Construire la chaîne formatée
        $formatted = " [" . implode(", ", $mainInfo) . "]";
        
        if (!empty($changes)) {
            $formatted .= " (Changes: " . implode(", ", $changes) . ")";
        }
        
        return $formatted . "\n" . $json;
    }

    /**
     * Journalise un événement générique avec formatage amélioré
     */
    public function logEvent(string $type, string $details): bool
    {
        try {
            $timestamp = date('Y-m-d H:i:s');
            $color = $this->getColorForEventType($type);
            $formattedDetails = $this->formatJson($details);
            
            // Format pour le fichier (sans couleurs)
            $plainEntry = "[{$timestamp}] [EVENT] {$type}:" . $formattedDetails . "\n\n";
            
            // Format coloré pour la console (si utilisé avec tail -f ou cat)
            $colorEntry = "{$this->colors['dim']}[{$timestamp}]{$this->colors['reset']} " .
                "[EVENT] " . 
                "{$color}{$this->colors['bold']}{$type}{$this->colors['reset']}:" . 
                $formattedDetails . "\n\n";
            
            // Écrire dans le fichier de logs (version sans couleurs)
            File::append($this->logFile, $plainEntry);
            
            // Également logger dans Laravel
            Log::info("{$type}: " . json_encode(json_decode($details), true));
            
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