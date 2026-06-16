<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorScheduleRequest;
use App\Models\DoctorSchedule;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role?->name === 'doctor') {
            $schedules = DoctorSchedule::where(
                'doctor_id',
                $user->doctor->id
            )->get();
        } else {
            $schedules = DoctorSchedule::with('doctor.user')->get();
        }

        return response()->json([
            'data' => $schedules,
        ]);
    }

    public function store(StoreDoctorScheduleRequest $request)
    {
        $user = $request->user();

        if (! in_array(
            $user->role?->name,
            ['admin', 'clinic_admin']
        )) {
            return response()->json([
                'message' => 'No autorizado.'
            ], 403);
        }

        $schedule = DoctorSchedule::create([
            ...$request->validated(),
            'slot_minutes' => $request->slot_minutes ?? 30,
        ]);

        AuditLogService::record(
            userId: $user->id,
            patientId: null,
            action: 'create_doctor_schedule',
            module: 'doctor_schedules',
            description: 'Horario médico creado',
            request: $request
        );

        return response()->json([
            'message' => 'Horario creado correctamente',
            'data' => $schedule
        ], 201);
    }
}
