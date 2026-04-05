<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $exists = DB::table('users')->where('username', 'admin')->exists();

        if (!$exists) {
            DB::table('users')->insert([
                'username'   => 'admin',
                'password'   => Hash::make('admin123'),
                'level'      => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('Admin user berhasil dibuat!');
        } else {
            $this->command->info('Admin user sudah ada, skip.');
        }
    }
}
