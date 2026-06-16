<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'business_name',
        'ruc',
        'address',
        'phone',
        'email',
        'logo_path',
        'primary_color',
        'secondary_color',
        'footer_text',
        'show_qr',
        'show_signature',
        'show_cmp',
    ];
    protected $casts = [
        'show_qr' => 'boolean',
        'show_signature' => 'boolean',
        'show_cmp' => 'boolean',
    ];
}
