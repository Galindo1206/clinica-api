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
}
