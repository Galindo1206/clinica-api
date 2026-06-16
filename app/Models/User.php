<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'phone',
        'document_type',
        'document_number',
        'birth_date',
        'gender',
        'address',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function uploadedMedicalDocuments()
    {
        return $this->hasMany(MedicalDocument::class, 'uploaded_by_user_id');
    }
    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active || ! $this->role) {
            return false;
        }

        $role = strtolower($this->role->name);

        if ($panel->getId() === 'reception') {
            return in_array($role, [
                'admin',
                'receptionist',
                'recepcionista',
            ]);
        }

        if ($panel->getId() === 'doctor') {
            return in_array($role, [
                'admin',
                'doctor',
                'medico',
                'médico',
            ]);
        }

        return $this->is_active
            && in_array($role, [
                'admin',
                'super_admin',
                'administrador',
                'receptionist',
                'recepcionista',
                'doctor',
                'medico',
                'médico',
            ]);
    }
}
