<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Sesiones\ActiveSesion;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticationController extends Controller
{
    /**
     
     *
     * @operationId Login
     */
    use ApiResponse;
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
            
            // ActiveSesion::updateOrCreate(['user_id'=>$user->id],[
            //     'roles'=>$user->getRoleNames()->toArray(),
            //     'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            //     'last_activity' => now()
            // ]);
            
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'error' => ['password' => ['La contraseña es incorrecta.']]
                ], 401);
            }
            
            $usuarios = [
                'status' => true,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60, // TTL en segundos
                'user' => $user,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name')
            ];
            
            return $this->success('Inicio de sesión exitoso', 200, $usuarios);
    
        } catch (\Throwable $th) {
            $this->error('Error al iniciar sesión');
            
        }

    }

    /**
     
     *
     * @operationId RefreshToken
     */
    public function refresh(){
        try {
            if (!$token = auth('api')->refresh()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se pudo refrescar el token'
                ], 401);
            }

            $user = auth('api')->user();
            $usuario = [
                'status' => true,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user' => $user,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name')
            ];
            return $this->success('Token refrescado exitosamente', 200, $usuario);
        } catch (\Throwable $th) {
            $this->error('Error al refrescar el token');
          
        }
    }

    /**
     
     *
     * @operationId Logout
     */
    public function logout(){
        try {
            auth('api')->logout();
            return $this->success('Se cerro sesión coreectamente',200);
        } catch (\Throwable $th) {
            $this->error('Error al cerrar sesión');
           
        }
    }

    /**
     * 
     * @operateId validateToken
     */
    public function validatedToken(Request $request){
        try {
        // Obtener usuario desde el token
        $user = JWTAuth::setToken($request->token)->authenticate();

        if (!$user) {
            return $this->error('Token válido pero usuario no encontrado', 404);
        }

        // Token válido y usuario encontrado
        return $this->success('Token válido', 200, [
            'user' => $user
        ]);

    } catch (JWTException $e) {
        // Error de token inválido, expirado o ausente
        return $this->error('Token inválido o expirado', 401);
    } catch (\Throwable $th) {
        // Cualquier otro error inesperado
        return $this->error('Error inesperado', 500);
    }
    }
}

