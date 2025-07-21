<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cargo786Controller;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Cargo786 API Routes
Route::prefix('cargo786')->group(function () {
    Route::post('/create-member', [Cargo786Controller::class, 'createMember']);
    Route::post('/create-order', [Cargo786Controller::class, 'createOrder']);
    Route::get('/address-list', [Cargo786Controller::class, 'getAddressList']);
    Route::get('/test-connection', [Cargo786Controller::class, 'testConnection']);
});
