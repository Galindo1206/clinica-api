<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'cmp_number',
        'specialty',
        'license_number',
        'professional_title',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accessPermissions()
    {
        return $this->hasMany(AccessPermission::class);
    }
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }
    public function unavailabilities()
    {
        return $this->hasMany(DoctorUnavailability::class);
    }
}
