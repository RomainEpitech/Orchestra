<?php

namespace App\Services;

use App\Models\Enterprise;
use Illuminate\Support\Str;

class KeyGeneratorService
{
    /**
     * Generate a unique key for enterprise recovery
     * 
     * @param int $length
     * @return string
     */
    public function generateUniqueKey(int $length = 12): string
    {
        $attempts = 0;
        $maxAttempts = 10;
        
        do {
            // Generate a random string with prefix to make it more identifiable
            $key = 'ent_' . Str::random($length);
            
            // Check if key already exists
            $exists = Enterprise::where('key', $key)->exists();
            
            $attempts++;
        } while ($exists && $attempts < $maxAttempts);
        
        // If we couldn't generate a unique key after max attempts, use timestamp
        if ($exists) {
            $key = 'ent_' . Str::random(6) . '_' . time();
        }
        
        return $key;
    }
}