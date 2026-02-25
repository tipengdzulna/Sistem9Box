<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['username' => 'superadmin'], [
            'name' => 'Super Admin',
            'password' => Hash::make('superadmin123'),
            'role' => 'super_admin',
            'ue1' => null,
        ]);
        User::updateOrCreate(['username' => 'admin'], [
            'name' => 'Admin Kemenkeu',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'ue1' => null,
        ]);
        User::updateOrCreate(['username' => 'operator_djp'], [
            'name' => 'Operator DJP',
            'password' => Hash::make('operator123'),
            'role' => 'operator',
            'ue1' => 4,
        ]);
    }
}
