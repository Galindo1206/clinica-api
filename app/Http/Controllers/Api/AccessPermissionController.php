<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccessPermissionRequest;
use App\Models\AccessPermission;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AccessPermissionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role?->name === 'patient') {
            $permissions = AccessPermission::with(['doctor.user'])
                ->where('patient_id', $user->patient->id)
                ->latest()
                ->get();
        } elseif ($user->role?->name === 'doctor') {
            $permissions = AccessPermission::with(['patient.user'])
                ->where('doctor_id', $user->doctor->id)
                ->latest()
                ->get();
        } else {
            $permissions = AccessPermission::with(['patient.user', 'doctor.user', 'grantedBy'])
                ->latest()
                ->get();
        }

        return response()->json([
            'data' => $permissions,
        ]);
    }

    public function store(StoreAccessPermissionRequest $request)
    {
        $user = $request->user();

        if (! in_array($user->role?->name, ['admin', 'clinic_admin', 'patient'])) {
            return response()->json([
                'message' => 'No autorizado para otorgar permisos.',
            ], 403);
        }

        $data = $request->validated();

        if ($user->role?->name === 'patient' && $user->patient->id !== (int) $data['patient_id']) {
            return response()->json([
                'message' => 'Solo puedes otorgar permisos sobre tu propia bóveda médica.',
            ], 403);
        }

        $permission = AccessPermission::updateOrCreate(
            [
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'],
            ],
            [
                'granted_by_user_id' => $user->id,
                'permission_type' => $data['permission_type'],
                'starts_at' => $data['starts_at'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
                'is_active' => true,
            ]
        );

        AuditLogService::record(
            userId: $user->id,
            patientId: $permission->patient_id,
            action: 'grant_access_permission',
            module: 'access_permissions',
            description: 'Usuario otorgó acceso a bóveda médica',
            request: $request
        );

        return response()->json([
            'message' => 'Permiso otorgado correctamente',
            'data' => $permission->load(['patient.user', 'doctor.user', 'grantedBy']),
        ], 201);
    }

    public function revoke(Request $request, AccessPermission $accessPermission)
    {
        $user = $request->user();

        if (! in_array($user->role?->name, ['admin', 'clinic_admin', 'patient'])) {
            return response()->json([
                'message' => 'No autorizado para revocar permisos.',
            ], 403);
        }

        if ($user->role?->name === 'patient' && $user->patient->id !== $accessPermission->patient_id) {
            return response()->json([
                'message' => 'Solo puedes revocar permisos sobre tu propia bóveda médica.',
            ], 403);
        }

        $accessPermission->update([
            'is_active' => false,
        ]);

        AuditLogService::record(
            userId: $user->id,
            patientId: $accessPermission->patient_id,
            action: 'revoke_access_permission',
            module: 'access_permissions',
            description: 'Usuario revocó acceso a bóveda médica',
            request: $request
        );

        return response()->json([
            'message' => 'Permiso revocado correctamente',
            'data' => $accessPermission,
        ]);
    }
}
