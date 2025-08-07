<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UsersCreateRequest;
use App\Models\Logs\Logs;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Helpers\CacheHelper;
use App\Http\Requests\RolesOrPermission\AsignarPermisosUsuarioRequest;

class UserController extends Controller
{
    //
    use ApiResponse;
    /**
     
     *
     * @operationId Lista Usuarios
     */
    public function index(Request $request)
    {
        try {
             // Usamos un cache key único para cada página/filtro
            $page = $request->get('page', 1);

            //ejemplo con cache
           $cacheKey = "api_users_page_{$page}";

            $user = CacheHelper::remember($cacheKey,600,function(){
                return  User::with(['roles'])->paginate(10);
            });


            //ejemplo sin cache
            //$user = User::with(['roles'])->paginate(10);
            $pagination = [
                'lastPage'=>$user->lastPage(),
                'currentPage'=>$user->currentPage(),
                'perPage'=>$user->perPage(),
                'total'=>$user->total()
            ];

            $userData = $user->map(function($row){
                return [
                    'id' => $row->id,
                    'name'=> $row->name,
                    'email' => $row->email
                ];
            });
           return $this->success('Lista de usuarios',200,$userData, $pagination);
        } catch (\Exception $e) {
            //throw $th;
            return $this->error('Error al cargar los usuarios');
        }
       
    }

    /**
     
     *
     * @operationId Crear Usuarios
     */
    public function createUser(UsersCreateRequest $request){
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $user = User::create([
                'name'=>$validated['name'],
                'email'=>$validated['email'],
                'password'=>Hash::make($validated['password'])
            ]);
            if($request->has('rol')){
                $user->assignRole($validated['rol']);
            }
            
            if($request->has('permisos')){
                foreach($validated['permisos'] as $permiso){
                    $user->givePermissionTo($permiso);
                }
            }

            Logs::create([
                'action' => 'create_user',
                'ip' => $request->ip(),
                'data' => $user->id
            ]);
            DB::commit();

            return $this->success('Usuario creado',200,$user);
        } catch (\Exception $e) {
            //throw $th;
           DB::rollBack();
            return $e->getMessage();
        }
    }

    /**
     
     *
     * @operationId Agregar permisos a usuarios
     */
   public function AgregarPermisoUsuario(AsignarPermisosUsuarioRequest $request, $userId){
    try {
        DB::beginTransaction();
        $validated = $request->validated();
        $user = User::find($userId);
        foreach($validated['permisos'] as $permiso){
            //validar si el permiso ya esta asignado al usuario
            if($user->hasPermissionTo($permiso)){
                continue;
            }

            $user->givePermissionTo($permiso);
        }
        DB::commit();
        return $this->success('Permisos asignados correctamente',200,$user);
    } catch (\Throwable $th) {
        //throw $th;
        DB::rollBack();
        return $this->error("Error al asignar permisos");
    }
   }

}
