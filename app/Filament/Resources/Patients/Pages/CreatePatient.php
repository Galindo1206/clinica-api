<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $patientRoleId = Role::where('name', 'patient')->value('id');

            $user = User::create([
                'role_id' => $patientRoleId,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make(Str::random(16)),
                'phone' => $data['phone'] ?? null,
                'document_type' => $data['document_type'] ?? null,
                'document_number' => $data['document_number'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'is_active' => true,
            ]);

            return Patient::create([
                'user_id' => $user->id,
                'blood_type' => $data['blood_type'] ?? null,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'insurance_name' => $data['insurance_name'] ?? null,
                'insurance_number' => $data['insurance_number'] ?? null,
            ]);
        });
    }
}
