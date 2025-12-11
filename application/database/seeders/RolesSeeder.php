<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create Super Admin role if not exists
        $role = Role::firstOrCreate(['name' => 'Super Admin']);

        // Create default user if not exists
        $user = User::firstOrCreate(
            ['email' => 'wasim.html@gmail.com'], // default email
            [
                'name' => 'Mohammad Wasim',
                'password' => bcrypt('913390'), // change password!
                'status' => 1,
            ]
        );

        // Assign role to user (if not already assigned)
        if (!$user->hasRole($role->name)) {
            $user->assignRole($role->name);
        }
    }
}
