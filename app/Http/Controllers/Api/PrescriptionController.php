<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePrescriptionRequest;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role?->name === 'patient') {
            $prescriptions = Prescription::with(['doctor.user', 'items', 'consultation'])
                ->where('patient_id', $user->patient->id)
                ->latest('issued_at')
                ->get();
        } elseif ($user->role?->name === 'doctor') {
            $prescriptions = Prescription::with(['patient.user', 'items', 'consultation'])
                ->where('doctor_id', $user->doctor->id)
                ->latest('issued_at')
                ->get();
        } else {
            $prescriptions = Prescription::with(['patient.user', 'doctor.user', 'items', 'consultation'])
                ->latest('issued_at')
                ->get();
        }

        return response()->json([
            'data' => $prescriptions,
        ]);
    }

    public function store(StorePrescriptionRequest $request)
    {
        $user = $request->user();

        if ($user->role?->name !== 'doctor') {
            return response()->json([
                'message' => 'Solo los médicos pueden emitir recetas.',
            ], 403);
        }

        $data = $request->validated();

        $consultation = Consultation::where('id', $data['consultation_id'])
            ->where('doctor_id', $user->doctor->id)
            ->first();

        if (! $consultation) {
            return response()->json([
                'message' => 'Consulta no encontrada o no pertenece al médico autenticado.',
            ], 404);
        }

        $prescription = Prescription::create([
            'consultation_id' => $consultation->id,
            'patient_id' => $consultation->patient_id,
            'doctor_id' => $user->doctor->id,
            'prescription_code' => 'RX-' . str_pad((Prescription::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT),
            'general_indications' => $data['general_indications'] ?? null,
            'issued_at' => $data['issued_at'],
        ]);

        foreach ($data['items'] as $item) {
            $prescription->items()->create($item);
        }

        AuditLogService::record(
            userId: $user->id,
            patientId: $prescription->patient_id,
            action: 'create_prescription',
            module: 'prescriptions',
            description: 'Médico emitió una receta electrónica',
            request: $request
        );

        return response()->json([
            'message' => 'Receta electrónica creada correctamente',
            'data' => $prescription->load(['patient.user', 'doctor.user', 'consultation', 'items']),
        ], 201);
    }

    public function show(Request $request, Prescription $prescription)
    {
        $user = $request->user();

        if ($user->role?->name === 'patient' && $prescription->patient_id !== $user->patient->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if ($user->role?->name === 'doctor' && $prescription->doctor_id !== $user->doctor->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        AuditLogService::record(
            userId: $user->id,
            patientId: $prescription->patient_id,
            action: 'view_prescription',
            module: 'prescriptions',
            description: 'Usuario visualizó una receta electrónica',
            request: $request
        );

        return response()->json([
            'data' => $prescription->load(['patient.user', 'doctor.user', 'consultation', 'items']),
        ]);
    }
}
