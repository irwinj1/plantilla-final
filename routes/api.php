<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\auth\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthenticationController::class, 'login']);
    Route::post('/logout',[AuthenticationController::class,'logout'])->middleware('rolePermission');
});

Route::middleware('auth:api')->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->middleware('rolePermission:Super Admin');
    Route::post('/', [UserController::class, 'createUser'])->middleware('rolePermission:Super Admin');
    Route::post('create-permission',[UserController::class,'createPermission'])->middleware('rolePermission:Super Admin');
    Route::post('/create-rol',[UserController::class,'createRol'])->middleware('rolePermission:Super Admin');
});