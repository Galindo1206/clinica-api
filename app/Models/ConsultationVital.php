<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationVital extends Model
{
    protected $fillable = [
        'consultation_id',
        'weight',
        'height',
        'heart_rate',
        'respiratory_rate',
        'temperature',
        'blood_pressure',
        'oxygen_saturation',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}
