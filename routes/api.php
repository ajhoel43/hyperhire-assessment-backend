<?php

use App\Http\Controllers\Api\AccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::get('/people/recommendation', [AccountController::class, 'getRecommendations']);
Route::post('/people/{accountId}/like', [AccountController::class, 'likeAccount']);
Route::post('/people/{accountId}/dislike', [AccountController::class, 'dislikeAccount']);
Route::get('/people/liked', [AccountController::class, 'getLikedAccounts']);