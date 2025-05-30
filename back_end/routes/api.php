<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource("/test", TestController::class);

// Auth
Route::prefix("/auth")->name("auth.")->group(function () {
    Route::post("register", [AuthController::class, 'register'])->name("register");
    Route::post("login", [AuthController::class, 'login'])->name("login");
});
