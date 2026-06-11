<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $fillable = [
        'patient_id',
        'record_code',
        'summary',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
