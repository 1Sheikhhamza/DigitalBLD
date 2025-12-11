<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    public function run()
    {
        // Avoid duplicate user creation
        if (!User::where('email', 'wasim.html@gmail.com')->exists()) {
            User::create([
                'name' => 'Mohammad Wasim',
                'first_name' => 'Mohammad',
                'last_name' => 'Wasim',
                'email' => 'wasim.html@gmail.com',
                'mobile' => '01922002381',
                'status' => 1,
                'user_type' => 'Admin',
                'password' => Hash::make('913390'), // You can change the password
            ]);
        }
    }
}
