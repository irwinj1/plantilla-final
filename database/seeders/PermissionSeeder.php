<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'create_role',
            'edit_role',
            'delete_role',
            'view_role',   // corregí "vier_role" a "view_role"
            'create_permission',
            'edit_permission',
            'delete_permission',
            'view_permission',
            'create_user',
            'edit_user',
            'delete_user',
            'view_user',
        ];
    
        $guards = ['web', 'api']; // queremos que existan para ambos guards
    
        foreach ($permissions as $permission) {
            foreach ($guards as $guard) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => $guard
                ]);
            }
        }
    }
}
