<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function show()
    {
        return response()->json([
            'data' => Setting::first(),
        ]);
    }

    public function update(UpdateSettingRequest $request)
    {
        $user = $request->user();

        if (! in_array($user->role?->name, ['admin', 'clinic_admin'])) {
            return response()->json([
                'message' => 'No autorizado para modificar la configuración.',
            ], 403);
        }

        $setting = Setting::firstOrFail();

        $setting->update($request->validated());

        AuditLogService::record(
            userId: $user->id,
            patientId: null,
            action: 'update_settings',
            module: 'settings',
            description: 'Usuario actualizó la configuración de la clínica',
            request: $request
        );

        return response()->json([
            'message' => 'Configuración actualizada correctamente',
            'data' => $setting,
        ]);
    }
}
