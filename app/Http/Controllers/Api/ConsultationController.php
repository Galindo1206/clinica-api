<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultationRequest;
use App\Models\Consultation;
use Illuminate\Http\Request;
use App\Services\AuditLogService;
use App\Services\AccessPermissionService;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role?->name === 'patient') {
            $consultations = Consultation::with(['doctor.user', 'vitals'])
                ->where('patient_id', $user->patient->id)
                ->latest('consultation_date')
                ->get();
        } elseif ($user->role?->name === 'doctor') {
            $consultations = Consultation::with(['patient.user', 'doctor.user', 'vitals'])
                ->where(function ($query) use ($user) {
                    $query->where('doctor_id', $user->doctor->id)
                        ->orWhereHas('patient.accessPermissions', function ($permissionQuery) use ($user) {
                            $permissionQuery->where('doctor_id', $user->doctor->id)
                                ->where('is_active', true)
                                ->where(function ($q) {
                                    $q->whereNull('starts_at')
                                        ->orWhere('starts_at', '<=', now());
                                })
                                ->where(function ($q) {
                                    $q->whereNull('expires_at')
                                        ->orWhere('expires_at', '>=', now());
                                });
                        });
                })
                ->latest('consultation_date')
                ->get();
        } else {
            $consultations = Consultation::with(['patient.user', 'doctor.user', 'vitals'])
                ->latest('consultation_date')
                ->get();
        }

        return response()->json([
            'data' => $consultations,
        ]);
    }

    public function store(StoreConsultationRequest $request)
    {
        $user = $request->user();

        if ($user->role?->name !== 'doctor') {
            return response()->json([
                'message' => 'Solo los médicos pueden registrar consultas.',
            ], 403);
        }

        $data = $request->validated();

        $consultation = Consultation::create([
            'patient_id' => $data['patient_id'],
            'doctor_id' => $user->doctor->id,
            'reason' => $data['reason'],
            'symptoms' => $data['symptoms'] ?? null,
            'diagnosis' => $data['diagnosis'] ?? null,
            'treatment' => $data['treatment'] ?? null,
            'observations' => $data['observations'] ?? null,
            'consultation_date' => $data['consultation_date'],
        ]);

        if (isset($data['vitals'])) {
            $consultation->vitals()->create($data['vitals']);
        }
        AuditLogService::record(
            userId: $user->id,
            patientId: $consultation->patient_id,
            action: 'create_consultation',
            module: 'consultations',
            description: 'Médico registró una consulta médica',
            request: $request
        );

        return response()->json([
            'message' => 'Consulta registrada correctamente',
            'data' => $consultation->load(['patient.user', 'doctor.user', 'vitals']),
        ], 201);
    }

    public function show(Request $request, Consultation $consultation)
    {
        $user = $request->user();

        if ($user->role?->name === 'patient' && $consultation->patient_id !== $user->patient->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if ($user->role?->name === 'doctor') {
            $isOwnConsultation = $consultation->doctor_id === $user->doctor->id;

            $hasPermission = AccessPermissionService::doctorCanAccessPatient(
                $user->doctor,
                $consultation->patient
            );

            if (! $isOwnConsultation && ! $hasPermission) {
                return response()->json(['message' => 'No autorizado.'], 403);
            }
        }
        AuditLogService::record(
            userId: $user->id,
            patientId: $consultation->patient_id,
            action: 'view_consultation',
            module: 'consultations',
            description: 'Usuario visualizó una consulta médica',
            request: $request
        );

        return response()->json([
            'data' => $consultation->load(['patient.user', 'doctor.user', 'vitals']),
        ]);
    }
}
