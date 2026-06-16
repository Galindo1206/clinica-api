<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class PrescriptionPdfController extends Controller
{
    public function generate(Prescription $prescription)
    {
        $prescription->load([
            'patient.user',
            'doctor.user',
            'items'
        ]);

        $setting = Setting::first();

        $pdf = Pdf::loadView(
            'pdf.prescription',
            compact(
                'prescription',
                'setting'
            )
        );

        return $pdf->download(
            $prescription->prescription_code . '.pdf'
        );
    }
}
