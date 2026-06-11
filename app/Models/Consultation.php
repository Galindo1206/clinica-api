<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'reason',
        'symptoms',
        'diagnosis',
        'treatment',
        'observations',
        'consultation_date',
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function vitals()
    {
        return $this->hasOne(ConsultationVital::class);
    }
}
