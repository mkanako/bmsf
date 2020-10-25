<?php

namespace Cc\Bmsf\Database\Seeds;

use Cc\Bmsf\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        if (!User::where('username', 'admin')->first()) {
            User::create([
                'username' => 'admin',
                'password' => Hash::make('admin'),
            ]);
        }
    }
}
