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
use App\Http\Requests\RolesOrPermission\CreateRolRequest;
use App\Http\Requests\RolesOrPermission\PermissionRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    //
    use ApiResponse;
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

    public function createUser(UsersCreateRequest $request){
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $user = User::create([
                'name'=>$validated['name'],
                'email'=>$validated['email'],
                'password'=>Hash::make($validated['password'])
            ]);
            $user->assignRole('Admin');

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

    public function createPermission(PermissionRequest $request){
        try {
            //code...
            $validated = $request->validated();

            DB::beginTransaction();
            $createdPermissions = [];
            
            foreach ($validated['name'] as $permission) {
                // Check if permission already exists
                if (!Permission::where('name', $permission)->exists()) {
                    $newPermission = Permission::create(['name' => $permission]);
                    $createdPermissions[] = $newPermission;
                }
            }
            
            DB::commit();

            if (empty($createdPermissions)) {
                return $this->error('Error al crear los permisos.', 401);
            }
            

            return $this->success('Permiso creado',200,$validated);

        } catch (\Exception $e) {
            //throw $th;
            return $this->error('Error al crear el permiso '+ $e->getMessage());
        }
    }

    public function createRol(CreateRolRequest $request){
        try {
            //code...
            $validated = $request->validated();
            DB::beginTransaction();
               $rol = Role::create([
                    'name'=>$validated['name']
                ]);
                //asignar permisos a rol
                $rol->syncPermissions($validated['permissions']);
            DB::commit();
          
            return $this->success('Rol creado',200,$validated);
        } catch (\Exception $e) {
            //throw $th;
            return $this->error('Error al crear el rol '+ $e->getMessage());
        }
    }

}
