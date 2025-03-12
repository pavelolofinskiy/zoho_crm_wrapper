<?php

// app/Http/Controllers/ZohoController.php
namespace App\Http\Controllers;

use App\Services\ZohoService;
use Illuminate\Http\Request;

class ZohoController extends Controller
{
    protected $zohoService;

    public function __construct(ZohoService $zohoService)
    {
        $this->zohoService = $zohoService;
    }

    public function createDealAndAccount(Request $request)
    {
        try {
            if (!preg_match('/^\\+?[0-9]{10,15}$/', $request->input('accountPhone'))) {
                throw new \Exception('Wrong phone number format');
            }
            
            $accountId = $this->zohoService->createAccount(
                $request->input('accountName'),
                $request->input('accountWebsite'),
                $request->input('accountPhone')
            );
            
            $this->zohoService->createDeal(
                $request->input('dealName'),
                $request->input('dealStage'),
                $accountId
            );
            
            return response()->json(['message' => 'Deal and Account created successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
