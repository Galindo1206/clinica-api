<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorUnavailabilityRequest;
use App\Models\DoctorUnavailability;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class DoctorUnavailabilityController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role?->name === 'doctor') {
            $items = DoctorUnavailability::where('doctor_id', $user->doctor->id)
                ->latest('starts_at')
                ->get();
        } else {
            $items = DoctorUnavailability::with('doctor.user')
                ->latest('starts_at')
                ->get();
        }

        return response()->json([
            'data' => $items,
        ]);
    }

    public function store(StoreDoctorUnavailabilityRequest $request)
    {
        $user = $request->user();

        if (! in_array($user->role?->name, ['admin', 'clinic_admin', 'doctor'])) {
            return response()->json([
                'message' => 'No autorizado para crear bloqueos de disponibilidad.',
            ], 403);
        }

        $data = $request->validated();

        if ($user->role?->name === 'doctor' && $user->doctor->id !== (int) $data['doctor_id']) {
            return response()->json([
                'message' => 'Solo puedes bloquear tu propia agenda.',
            ], 403);
        }

        $item = DoctorUnavailability::create([
            'doctor_id' => $data['doctor_id'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'reason' => $data['reason'] ?? null,
            'is_active' => true,
        ]);

        AuditLogService::record(
            userId: $user->id,
            patientId: null,
            action: 'create_doctor_unavailability',
            module: 'doctor_unavailabilities',
            description: 'Usuario creó un bloqueo de disponibilidad médica',
            request: $request
        );

        return response()->json([
            'message' => 'Bloqueo de disponibilidad creado correctamente',
            'data' => $item->load('doctor.user'),
        ], 201);
    }

    public function cancel(Request $request, DoctorUnavailability $doctorUnavailability)
    {
        $user = $request->user();

        if (! in_array($user->role?->name, ['admin', 'clinic_admin', 'doctor'])) {
            return response()->json([
                'message' => 'No autorizado para cancelar bloqueos de disponibilidad.',
            ], 403);
        }

        if ($user->role?->name === 'doctor' && $user->doctor->id !== $doctorUnavailability->doctor_id) {
            return response()->json([
                'message' => 'Solo puedes cancelar bloqueos de tu propia agenda.',
            ], 403);
        }

        $doctorUnavailability->update([
            'is_active' => false,
        ]);

        AuditLogService::record(
            userId: $user->id,
            patientId: null,
            action: 'cancel_doctor_unavailability',
            module: 'doctor_unavailabilities',
            description: 'Usuario canceló un bloqueo de disponibilidad médica',
            request: $request
        );

        return response()->json([
            'message' => 'Bloqueo cancelado correctamente',
            'data' => $doctorUnavailability,
        ]);
    }
}
