<?php
use App\Http\Controllers\auth\AuthenticationController;
use App\Http\Controllers\auth\RolPermissionController;
use App\Http\Controllers\auth\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthenticationController::class, 'login']);
    Route::post('/logout',[AuthenticationController::class,'logout'])->middleware('rolePermission:Super Admin,Admin');
    Route::post('/refresh',[AuthenticationController::class,'refresh'])->middleware('rolePermission:Super Admin,Admin');
    Route::post('/validate-token',[AuthenticationController::class,'validatedToken']);
});

Route::middleware('auth:api')->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->middleware('rolePermission:Super Admin');
    Route::post('/', [UserController::class, 'createUser'])->middleware('rolePermission:Super Admin');
    Route::post('/agregar-permisos/{userId}',[UserController::class,'AgregarPermisoUsuario'])->middleware('rolePermission:Super Admin');
    Route::post('/asignar-rol/{userId}',[UserController::class,'AsignarRolUsuario'])->middleware('rolePermission:Super Admin');
    Route::post('/revocar-rol/{userId}',[UserController::class,'RevocarRolUsuario'])->middleware('rolePermission:Super Admin');
    Route::post('/revocar-permisos/{userId}',[UserController::class,'RevocarPermisoUsuario'])->middleware('rolePermission:Super Admin');
});

Route::middleware('auth:api')->prefix('rol-permisos')->group(function () {
    Route::get('/lista-permisos',[RolPermissionController::class,'ListPermission'])->middleware('rolePermission:Super Admin');
    Route::get('/lista-roles',[RolPermissionController::class,'ListRole'])->middleware('rolePermission:Super Admin');
    Route::post('create-permission',[RolPermissionController::class,'createPermission'])->middleware('rolePermission:Super Admin');
    Route::post('/create-rol',[RolPermissionController::class,'createRol'])->middleware('rolePermission:Super Admin');
    Route::delete('/eliminar-rol/{id}',[RolPermissionController::class,'eliminarRol'])->middleware('rolePermission:Super Admin');
    Route::delete('/eliminar-permiso',[RolPermissionController::class,'eliminarPermisos'])->middleware('rolePermission:Super Admin');
});