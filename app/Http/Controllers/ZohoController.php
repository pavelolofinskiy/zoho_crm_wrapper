<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ZohoController extends Controller
{
    public function createDealAndAccount(Request $request)
    {
        $accessToken = $this->refreshAccessToken(); 
        try {

            if ($this->phoneValidator($request->input('accountPhone')) == false) {
                throw new \Exception(json_encode('wrong phone number format'));
            }


            // Создание аккаунта
            $accountResponse = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $accessToken
            ])->post('https://www.zohoapis.eu/crm/v2/Accounts', [
                'data' => [
                    [
                        'Account_Name' => $request->input('accountName'),
                        'Website' => $request->input('accountWebsite'),
                        'Phone' => $request->input('accountPhone'),
                    ]
                ]
            ]);

            if ($accountResponse->failed()) {
                throw new \Exception($accountResponse->body());
            }
            
            $accountData = $accountResponse->json();

            if ($accountData['data'][0]['status'] == 'error') {
                throw new \Exception(json_encode($accountData));
            }

            

            $accountId = $accountData['data'][0]['details']['Created_By']['id'];

            // Создание сделки
            $dealResponse = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $accessToken
            ])->post('https://www.zohoapis.eu/crm/v2/Deals', [
                'data' => [
                    [
                        'Deal_Name' => $request->input('deal_name'),
                        'Stage' => $request->input('deal_stage'),
                        'Account_Name' => ['id' => $accountId]
                    ]
                ]
            ]);

            if ($dealResponse->failed()) {
                throw new \Exception($dealResponse->body());
            }

            return response()->json(['message' => 'Deal and Account created successfully']);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function refreshAccessToken()
    {
        try {
            $storedRefreshToken = env('ZOHO_REFRESH_TOKEN'); // Инициализация переменной

            $response = Http::asForm()->post('https://accounts.zoho.eu/oauth/v2/token', [
                'client_id' => env('ZOHO_CLIENT_ID'),
                'client_secret' => env('ZOHO_CLIENT_SECRET'),
                'refresh_token' => $storedRefreshToken,
                'grant_type' => 'refresh_token'
            ]);
            
            if ($response->failed()) {
                throw new \Exception($response->body());
            }

            $data = $response->json();

            if (isset($data['access_token'])) {
                return $data['access_token'];
            } else {
                throw new \Exception('Access token not found in response');
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error refreshing access token', 'details' => $e->getMessage()], 400);
        }
    }

    public function handleZohoCallback(Request $request)
    {
        $accessToken = $this->getAccessToken($request);
        return response()->json(['access_token' => $accessToken]);
    }

    public function phoneValidator($phone) {
        return preg_match('/^\+?[0-9]{10,15}$/', $phone) === 1;
    }
}
