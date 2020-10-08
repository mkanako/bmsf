<?php

namespace Cc\Labems\Database\Seeds;

use Cc\Labems\Models\User;
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
