<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'username'   => 'admin',
            'password'   => Hash::make('admin123'),
            'level'      => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}