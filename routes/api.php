<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PingController;
use App\Http\Controllers\Api\DnsLookupController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/ping', [PingController::class, 'handle']);
Route::get('/dns', [DnsLookupController::class, 'handle']);