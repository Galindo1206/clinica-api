<?php

namespace App\Filament\Reception\Resources\Patients\Pages;

use App\Filament\Reception\Resources\Patients\PatientResource;
use App\Models\MedicalRecord;
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
        return DB::transaction(function () use ($data): Patient {
            $user = User::create([
                'role_id' => Role::where('name', 'patient')->value('id'),
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make(Str::random(16)),
                'phone' => $data['phone'] ?? null,
                'document_type' => $data['document_type'] ?? null,
                'document_number' => $data['document_number'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'is_active' => true,
            ]);

            $patient = Patient::create(['user_id' => $user->id]);

            MedicalRecord::create([
                'patient_id' => $patient->id,
                'record_code' => 'HC-' . str_pad($patient->id, 6, '0', STR_PAD_LEFT),
                'summary' => null,
            ]);

            return $patient;
        });
    }
}
