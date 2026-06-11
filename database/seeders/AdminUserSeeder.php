<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use app\Models\Role;
use Illuminate\Support\Facades\Hash;



class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        User::create([
            'role_id' => $adminRole->id,
            'name' => 'Administrador',
            'email' => 'admin@clinica.test',
            'password' => Hash::make('password123'),
            'document_type' => 'DNI',
            'document_number' => '00000000',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
