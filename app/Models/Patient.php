<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'blood_type',
        'emergency_contact_name',
        'emergency_contact_phone',
        'insurance_name',
        'insurance_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function medicalDocuments()
    {
        return $this->hasMany(MedicalDocument::class);
    }

    public function accessPermissions()
    {
        return $this->hasMany(AccessPermission::class);
    }
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }
}
