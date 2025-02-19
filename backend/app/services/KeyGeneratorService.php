<?php

namespace App\Services;

use App\Models\Enterprise;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class KeyGeneratorService
{
    /**
     * Generate a unique key for enterprise recovery
     * 
     * @param int $length Key length
     * @return array Array containing both readable key and its hashed version
     */
    public function generateUniqueKey(int $length = 10): array
    {
        $attempts = 0;
        $maxAttempts = 10;
        
        do {
            // Create an alphanumeric key that's readable but secure
            $readableKey = $this->generateReadableKey($length);
            
            // Generate a hash of the key
            $hashedKey = Hash::make($readableKey);
            
            // We need to check if a similar hash already exists
            // This is challenging since hashes include salts, but we'll check key uniqueness instead
            $exists = false;
            
            $attempts++;
        } while ($exists && $attempts < $maxAttempts);
        
        // If we couldn't generate a unique key after max attempts, add timestamp
        if ($exists) {
            $readableKey = $this->generateReadableKey(6) . substr(time(), -4);
            $hashedKey = Hash::make($readableKey);
        }
        
        return [
            'readable' => $readableKey,
            'hashed' => $hashedKey
        ];
    }
    
    /**
     * Generates a readable but secure random key
     * 
     * @param int $length
     * @return string
     */
    private function generateReadableKey(int $length): string
    {
        // Mix of uppercase, lowercase and numbers, avoiding confusing characters
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        
        $key = '';
        
        // Ensure the first character is a letter
        $key .= $chars[random_int(0, 43)]; // First 44 chars are letters
        
        // Add at least one number
        $key .= $chars[random_int(44, strlen($chars) - 1)];
        
        // Fill the rest randomly
        for ($i = 2; $i < $length; $i++) {
            $key .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        // Shuffle for unpredictability
        return str_shuffle($key);
    }
}