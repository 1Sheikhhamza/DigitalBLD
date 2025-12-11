<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $defaultActions = ['list', 'create', 'edit', 'delete', 'view'];

        $modules = Module::all();

        foreach ($modules as $module) {
            foreach ($defaultActions as $action) {
                Permission::firstOrCreate(
                    [
                        'name' => strtolower("{$module->name}.{$action}"),
                        'slug' => strtolower("{$module->name}.{$action}"),
                        'guard_name' => 'web',
                        'module_id' => $module->id,
                    ]
                );
            }
        }
    }
}
