<?php

namespace App\Services;

use App\Jobs\LogSystemEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class SystemLoggerService
{
    /**
     * Path to the log file
     * 
     * @var string
     */
    protected $logFile;

    /**
     * ANSI colors for the terminal
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
     * Colors per event type
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
     * Constructor
     */
    public function __construct()
    {
        // Create a logs directory in storage
        $this->logFile = '/var/www/logs/journal.log';
        
        // Ensure the directory exists
        $logDir = dirname($this->logFile);
        if (!File::exists($logDir)) {
            File::makeDirectory($logDir, 0755, true);
        }
    }

    /**
     * Logs an enterprise event
     */
    public function logEnterpriseEvent(string $action, array $data): bool
    {
        $details = json_encode($data, JSON_PRETTY_PRINT);
        // Dispatch the job instead of executing it directly
        LogSystemEvent::dispatch("ENTERPRISE_{$action}", $details);
        return true;
    }

    /**
     * Logs a license event
     */
    public function logLicenseEvent(string $action, array $data): bool
    {
        $details = json_encode($data, JSON_PRETTY_PRINT);
        LogSystemEvent::dispatch("LICENSE_{$action}", $details);
        return true;
    }

    /**
     * Logs a module event
     */
    public function logModuleEvent(string $action, array $data): bool
    {
        $details = json_encode($data, JSON_PRETTY_PRINT);
        LogSystemEvent::dispatch("MODULE_{$action}", $details);
        return true;
    }

    /**
     * Logs a role event
     */
    public function logRoleEvent(string $action, array $data): bool
    {
        $details = json_encode($data, JSON_PRETTY_PRINT);
        LogSystemEvent::dispatch("ROLE_{$action}", $details);
        return true;
    }

    /**
     * Logs a database operation
     */
    public function logDbOperation(string $operation, string $table, array $data): bool
    {
        $details = json_encode($data, JSON_PRETTY_PRINT);
        LogSystemEvent::dispatch("DB_{$operation}_{$table}", $details);
        return true;
    }

    /**
     * Determines the color for an event type
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
     * Formats JSON details for better readability
     */
    protected function formatJson(string $json): string
    {
        $data = json_decode($json, true);
        if (!$data) return $json;
        
        // Extract main information
        $mainInfo = [];
        
        // Key information to extract based on context
        if (isset($data['name'])) $mainInfo[] = "name: " . $data['name'];
        if (isset($data['fullname'])) $mainInfo[] = "fullname: " . $data['fullname'];
        if (isset($data['email'])) $mainInfo[] = "email: " . $data['email'];
        if (isset($data['module_name'])) $mainInfo[] = "module: " . $data['module_name'];
        if (isset($data['enterprise_name'])) $mainInfo[] = "enterprise: " . $data['enterprise_name'];
        if (isset($data['role_name'])) $mainInfo[] = "role: " . $data['role_name'];

        // Extract main changes
        $changes = [];
        if (isset($data['changed_attributes'])) {
            foreach ($data['changed_attributes'] as $key => $value) {
                $changes[] = "$key → $value";
            }
        }
        
        // Build the formatted string
        $formatted = " [" . implode(", ", $mainInfo) . "]";
        
        if (!empty($changes)) {
            $formatted .= " (Changes: " . implode(", ", $changes) . ")";
        }
        
        return $formatted . "\n" . $json;
    }

    /**
     * Logs a generic event with enhanced formatting
     */
    public function logEvent(string $type, string $details): bool
    {
        try {
            $timestamp = date('Y-m-d H:i:s');
            $color = $this->getColorForEventType($type);
            $formattedDetails = $this->formatJson($details);
            
            // Format for the file (without colors)
            $plainEntry = "[{$timestamp}] [EVENT] {$type}:" . $formattedDetails . "\n\n";
            
            // Colored format for the console (if used with tail -f or cat)
            $colorEntry = "{$this->colors['dim']}[{$timestamp}]{$this->colors['reset']} " .
                "[EVENT] " . 
                "{$color}{$this->colors['bold']}{$type}{$this->colors['reset']}:" . 
                $formattedDetails . "\n\n";
            
            // Write to the log file (plain version)
            File::append($this->logFile, $plainEntry);
            
            // Also log in Laravel
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
