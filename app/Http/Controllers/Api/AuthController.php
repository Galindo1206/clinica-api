<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\RegisterPatientRequest;
use App\Http\Requests\RegisterDoctorRequest;
use App\Http\Requests\LoginRequest;
use App\Services\AuditLogService;

class AuthController extends Controller
{
    public function registerPatient(RegisterPatientRequest $request)
    {
        $data = $request->validated();

        $patientRole = Role::where('name', 'patient')->firstOrFail();

        $user = User::create([
            'role_id' => $patientRole->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'document_type' => $data['document_type'] ?? null,
            'document_number' => $data['document_number'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'gender' => $data['gender'] ?? null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $patient = Patient::create([
            'user_id' => $user->id,
        ]);

        $medicalRecord = MedicalRecord::create([
            'patient_id' => $patient->id,
            'record_code' => 'HC-' . str_pad($patient->id, 6, '0', STR_PAD_LEFT),
            'summary' => null,
        ]);

        $token = $user->createToken('patient-token')->plainTextToken;

        return response()->json([
            'message' => 'Paciente registrado correctamente',
            'token' => $token,
            'user' => $user->load('role', 'patient'),
            'medical_record' => $medicalRecord,
        ], 201);
    }

    public function registerDoctor(RegisterDoctorRequest $request)
    {
        $data = $request->validated();

        $doctorRole = Role::where('name', 'doctor')->firstOrFail();

        $user = User::create([
            'role_id' => $doctorRole->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'document_type' => $data['document_type'] ?? null,
            'document_number' => $data['document_number'] ?? null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $doctor = Doctor::create([
            'user_id' => $user->id,
            'cmp_number' => $data['cmp_number'],
            'specialty' => $data['specialty'],
            'license_number' => $data['license_number'] ?? null,
            'professional_title' => $data['professional_title'] ?? null,
        ]);

        $token = $user->createToken('doctor-token')->plainTextToken;

        return response()->json([
            'message' => 'Médico registrado correctamente',
            'token' => $token,
            'user' => $user->load('role', 'doctor'),
            'doctor' => $doctor,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::with('role', 'patient', 'doctor')
            ->where('email', $data['email'])
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son correctas.'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json([
                'message' => 'Usuario inactivo.',
            ], 403);
        }

        $token = $user->createToken('auth-token')->plainTextToken;
        AuditLogService::record(
            userId: $user->id,
            patientId: $user->patient?->id,
            action: 'login',
            module: 'auth',
            description: 'Usuario inició sesión correctamente',
            request: $request
        );

        return response()->json([
            'message' => 'Login correcto',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('role', 'patient', 'doctor'),
        ]);
    }

    public function logout(Request $request)
    {
        AuditLogService::record(
            userId: $request->user()->id,
            patientId: $request->user()->patient?->id,
            action: 'logout',
            module: 'auth',
            description: 'Usuario cerró sesión',
            request: $request
        );
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente',
        ]);
    }
}
