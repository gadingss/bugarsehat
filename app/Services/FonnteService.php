<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    /**
     * Send a WhatsApp message using Fonnte API
     *
     * @param string $target Phone number
     * @param string $message The message body
     * @return bool
     */
    public static function sendMessage($target, $message)
    {
        $token = env('FONNTE_TOKEN');
        
        if (empty($token)) {
            Log::warning('Fonnte API Token is not set in .env. FonnteService aborted. Request the token from Fonnte.com');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Default to Indonesia
            ]);

            if ($response->successful()) {
                Log::info("Fonnte WA sent to $target successfully.");
                return true;
            } else {
                Log::error("Fonnte API Error: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Fonnte Exception: " . $e->getMessage());
            return false;
        }
    }
}
