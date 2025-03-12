<?php 
// app/Services/ZohoService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ZohoService
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function createAccount(string $name, string $website, string $phone)
    {
        $this->validateAccountData($name, $website, $phone);

        $accessToken = $this->authService->refreshAccessToken();
        
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken
        ])->post('https://www.zohoapis.eu/crm/v2/Accounts', [
            'data' => [[
                'Account_Name' => $name,
                'Website' => $website,
                'Phone' => $phone,
            ]]
        ]);
        
        if ($response->failed()) {
            throw new \Exception($response->body());
        }
        print_r($response->body()); die;
        return $response->json()['data'][0]['details']['id'];
    }

    public function createDeal(string $name, string $stage, string $accountId)
    {
        $this->validateDealData($name, $stage, $accountId);

        $accessToken = $this->authService->refreshAccessToken();
        
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken
        ])->post('https://www.zohoapis.eu/crm/v2/Deals', [
            'data' => [[
                'Deal_Name' => $name,
                'Stage' => $stage,
                'Account_Name' => ['id' => $accountId],
            ]]
        ]);
        
        if ($response->failed()) {
            throw new \Exception($response->body());
        }
    }

    private function validateAccountData(string $name, string $website, string $phone)
    {
        if (!empty($website)) {
            if (!preg_match('/^https?:\/\//', $website)) {
                $website = 'https://' . $website;
            }
    
            // Проверяем, содержит ли URL допустимое доменное расширение
            if (!preg_match('/\.(com|eu|net|org|co|io|ua|ru|de|fr|uk|pl|it|es|nl|cz|sk|ro|hu|se|no|dk|fi|be|at|ch)$/i', parse_url($website, PHP_URL_HOST) ?? '')) {
                throw ValidationException::withMessages([
                    'website' => ['Invalid domain extension'],
                ]);
            }
        }

        $validator = Validator::make([
            'name' => $name,
            'website' => $website,
            'phone' => $phone,
        ], [
            'name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function validateDealData(string $name, string $stage, string $accountId)
    {
        $validator = Validator::make([
            'name' => $name,
            'stage' => $stage,
            'accountId' => $accountId,
        ], [
            'name' => 'required|string|max:255',
            'stage' => 'required|string|max:100',
            'accountId' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
