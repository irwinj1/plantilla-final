<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Sesiones\ActiveSesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    /**
     
     *
     * @operationId login
     */
    public function login(Request $request){
        try {
            $messages = [
                'email.required' => 'El correo es obligatorio.',
                'email.email' => 'El correo no es válido.',
                'email.exists' => 'El correo no está registrado.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
           
            ];
    
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|min:8',
            ], $messages);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $credentials = $request->only('email', 'password');
    
            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Credenciales inválidas'
                ], 401);
            }
    
            $user = auth('api')->user();
            ActiveSesion::updateOrCreate(['user_id'=>$user->id],[
                'roles'=>$user->getRoleNames()->toArray(),
                'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                'last_activity' => now()
            ]);
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'error' => ['password' => ['La contraseña es incorrecta.']]
                ], 401);
            }
    
            return response()->json([
                'status' => true,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60, // TTL en segundos
                'user' => $user,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name')
            ], 200);
    
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error en el servidor',
                'error' => $th->getMessage()
            ], 500);
        }

    }

    public function refresh(){
        try {
            if (!$token = auth('api')->refresh()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se pudo refrescar el token'
                ], 401);
            }

            $user = auth('api')->user();

            return response()->json([
                'status' => true,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user' => $user,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name')
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al refrescar el token',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(){
        try {
            auth('api')->logout();
            return response()->json([
               'status' => true,
               'message' => 'Sesión cerrada correctamente'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
               'status' => false,
               'message' => 'Error al cerrar sesión',
              'error' => $th->getMessage()
            ], 500);
        }
    }
}

