<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrador general del sistema',
                'is_active' => true,
            ],
            [
                'name' => 'patient',
                'description' => 'Paciente con acceso a su bóveda médica',
                'is_active' => true,
            ],
            [
                'name' => 'doctor',
                'description' => 'Médico autorizado para revisar historias clínicas',
                'is_active' => true,
            ],
            [
                'name' => 'receptionist',
                'description' => 'Recepcionista con acceso operativo al panel',
                'is_active' => true,
            ],
            [
                'name' => 'clinic_admin',
                'description' => 'Administrador de clínica o consultorio',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                [
                    'description' => $role['description'],
                    'is_active' => $role['is_active'],
                ],
            );
        }
    }
}
