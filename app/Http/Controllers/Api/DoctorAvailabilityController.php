<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;

class DoctorAvailabilityController extends Controller
{
    public function show(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
        ]);

        return response()->json([
            'data' => AvailabilityService::getDoctorAvailability(
                $doctor,
                $data['date']
            ),
        ]);
    }
}
