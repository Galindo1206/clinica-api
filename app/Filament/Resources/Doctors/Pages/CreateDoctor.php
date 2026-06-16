<?php

namespace App\Filament\Resources\Doctors\Pages;

use App\Filament\Resources\Doctors\DoctorResource;
use App\Models\Doctor;
use App\Models\Role;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateDoctor extends CreateRecord
{
    protected static string $resource = DoctorResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $doctorRoleId = Role::where('name', 'doctor')->value('id');

            $user = User::create([
                'role_id' => $doctorRoleId,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make(Str::random(16)),
                'phone' => $data['phone'] ?? null,
                'document_type' => $data['document_type'] ?? null,
                'document_number' => $data['document_number'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            return Doctor::create([
                'user_id' => $user->id,
                'cmp_number' => $data['cmp_number'],
                'specialty' => $data['specialty'],
                'license_number' => $data['license_number'] ?? null,
                'professional_title' => $data['professional_title'] ?? null,
            ]);
        });
    }
}
