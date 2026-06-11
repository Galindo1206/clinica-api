<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalDocument extends Model
{
    protected $fillable = [
        'patient_id',
        'uploaded_by_user_id',
        'document_type',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_mime_type',
        'file_size',
        'document_date',
        'is_private',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
