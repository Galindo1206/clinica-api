<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'reason' => ['required', 'string', 'max:255'],
            'symptoms' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'treatment' => ['nullable', 'string'],
            'observations' => ['nullable', 'string'],
            'consultation_date' => ['required', 'date'],

            'vitals' => ['nullable', 'array'],
            'vitals.weight' => ['nullable', 'numeric', 'min:0'],
            'vitals.height' => ['nullable', 'numeric', 'min:0'],
            'vitals.heart_rate' => ['nullable', 'integer', 'min:0'],
            'vitals.respiratory_rate' => ['nullable', 'integer', 'min:0'],
            'vitals.temperature' => ['nullable', 'numeric', 'min:0'],
            'vitals.blood_pressure' => ['nullable', 'string', 'max:50'],
            'vitals.oxygen_saturation' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }
}
