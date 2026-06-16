<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentStatusRequest;
use App\Models\Appointment;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use App\Services\AvailabilityService;


class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role?->name === 'patient') {
            $appointments = Appointment::with(['doctor.user'])
                ->where('patient_id', $user->patient->id)
                ->latest('scheduled_at')
                ->get();
        } elseif ($user->role?->name === 'doctor') {
            $appointments = Appointment::with(['patient.user'])
                ->where('doctor_id', $user->doctor->id)
                ->latest('scheduled_at')
                ->get();
        } else {
            $appointments = Appointment::with(['patient.user', 'doctor.user'])
                ->latest('scheduled_at')
                ->get();
        }

        return response()->json([
            'data' => $appointments,
        ]);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $user = $request->user();

        if ($user->role?->name !== 'patient') {
            return response()->json([
                'message' => 'Solo los pacientes pueden solicitar citas.',
            ], 403);
        }

        $data = $request->validated();

        $duration = $data['duration_minutes'] ?? 30;
        $scheduledAt = $data['scheduled_at'];
        $availability = AvailabilityService::getDoctorAvailability(
            doctor: \App\Models\Doctor::findOrFail($data['doctor_id']),
            date: \Carbon\Carbon::parse($scheduledAt)->format('Y-m-d')
        );

        $requestedTime = \Carbon\Carbon::parse($scheduledAt)->format('H:i');

        $slot = collect($availability['slots'])->firstWhere('time', $requestedTime);

        if (! $slot) {
            return response()->json([
                'message' => 'El horario solicitado no está dentro del horario de atención del médico.',
            ], 422);
        }

        if (! $slot['available']) {
            return response()->json([
                'message' => 'El horario solicitado ya no está disponible.',
            ], 422);
        }


        $appointment = Appointment::create([
            'patient_id' => $user->patient->id,
            'doctor_id' => $data['doctor_id'],
            'reason' => $data['reason'],
            'notes' => $data['notes'] ?? null,
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => $duration,
            'status' => 'pending',
        ]);

        AuditLogService::record(
            userId: $user->id,
            patientId: $appointment->patient_id,
            action: 'create_appointment',
            module: 'appointments',
            description: 'Paciente solicitó una cita médica',
            request: $request
        );

        return response()->json([
            'message' => 'Cita solicitada correctamente',
            'data' => $appointment->load(['patient.user', 'doctor.user']),
        ], 201);
    }

    public function show(Request $request, Appointment $appointment)
    {
        $user = $request->user();

        if ($user->role?->name === 'patient' && $appointment->patient_id !== $user->patient->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if ($user->role?->name === 'doctor' && $appointment->doctor_id !== $user->doctor->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        AuditLogService::record(
            userId: $user->id,
            patientId: $appointment->patient_id,
            action: 'view_appointment',
            module: 'appointments',
            description: 'Usuario visualizó una cita médica',
            request: $request
        );

        return response()->json([
            'data' => $appointment->load(['patient.user', 'doctor.user']),
        ]);
    }

    public function updateStatus(UpdateAppointmentStatusRequest $request, Appointment $appointment)
    {
        $user = $request->user();

        if (! in_array($user->role?->name, ['doctor', 'admin', 'clinic_admin'])) {
            return response()->json([
                'message' => 'No autorizado para cambiar el estado de la cita.',
            ], 403);
        }

        if ($user->role?->name === 'doctor' && $appointment->doctor_id !== $user->doctor->id) {
            return response()->json([
                'message' => 'Solo puedes actualizar tus propias citas.',
            ], 403);
        }

        $data = $request->validated();

        $appointment->update([
            'status' => $data['status'],
            'status_reason' => $data['status_reason'] ?? null,
        ]);

        AuditLogService::record(
            userId: $user->id,
            patientId: $appointment->patient_id,
            action: 'update_appointment_status',
            module: 'appointments',
            description: 'Usuario actualizó el estado de una cita médica',
            request: $request
        );

        return response()->json([
            'message' => 'Estado de cita actualizado correctamente',
            'data' => $appointment->load(['patient.user', 'doctor.user']),
        ]);
    }
}
