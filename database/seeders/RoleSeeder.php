<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Super Admin', 'Admin', 'User'];
        $guards = ['web', 'api'];
    
        // ✅ 1. Crear roles para web y api
        foreach ($roles as $role) {
            foreach ($guards as $guard) {
                Role::firstOrCreate([
                    'name' => $role,
                    'guard_name' => $guard
                ]);
            }
        }
    
        // ✅ 2. Asignar permisos al rol Admin (para ambos guards)
        $adminPermissions = [
            'create_permission',
            'edit_permission',
            'delete_permission',
            'view_permission',
            'create_user',
            'edit_user',
            'delete_user',
            'view_user',
        ];
    
        foreach ($guards as $guard) {
            $adminRole = Role::where('name', 'Admin')->where('guard_name', $guard)->first();
            if ($adminRole) {
                $adminRole->syncPermissions($adminPermissions);
            }
        }
    
        // ✅ 3. Asignar permisos al rol User (para ambos guards)
        $userPermissions = [
            'view_permission',
            'view_user',
        ];
    
        foreach ($guards as $guard) {
            $userRole = Role::where('name', 'User')->where('guard_name', $guard)->first();
            if ($userRole) {
                $userRole->syncPermissions($userPermissions);
            }
        }
    }
}
