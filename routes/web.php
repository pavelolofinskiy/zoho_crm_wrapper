<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZohoController;

Route::get('/', function () {
    return view('home');
});

Route::post('/create-deal-and-account', [ZohoController::class, 'createDealAndAccount']);
Route::get('/callback', [ZohoController::class, 'handleZohoCallback']);