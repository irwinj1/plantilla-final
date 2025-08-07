<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RolesOrPermission\CreatePermissionRequest;
use App\Http\Requests\RolesOrPermission\CreateRolRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolPermissionController extends Controller
{
    use ApiResponse;

    //
     /**
     
     *
     * @operationId Listar roles
     */
    public function ListRole(){
        try {
            $roles = Role::all();
            return $this->success('Roles',200,$roles);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error("Error al listar los roles");
        }
    }

    //
     /**
     
     *
     * @operationId Listar Permisos
     */
    public function ListPermission(){
        try {
            $permissions = Permission::all();
            return $this->success('Permisos',200,$permissions);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error("Error al listar los permisos");
        }
    }

    //
     /**
     
     *
     * @operationId Crear Permisos
     */
    public function createPermission(CreatePermissionRequest $request){
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

    /**
     
     *
     * @operationId Crear Rol
     */
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
