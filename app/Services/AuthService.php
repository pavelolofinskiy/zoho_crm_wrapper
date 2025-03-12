<?php 
// app/Services/AuthService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class AuthService
{
    public function refreshAccessToken()
    {
        $response = Http::asForm()->post('https://accounts.zoho.eu/oauth/v2/token', [
            'client_id' => config('services.zoho.client_id'),
            'client_secret' => config('services.zoho.client_secret'),
            'refresh_token' => config('services.zoho.refresh_token'),
            'grant_type' => 'refresh_token'
        ]);
        
        if ($response->failed()) {
            throw new \Exception($response->body());
        }
        
        return $response->json()['access_token'] ?? throw new \Exception('Access token not found');
    }
}
