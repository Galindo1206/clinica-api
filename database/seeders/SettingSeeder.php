<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        Setting::updateOrCreate(
            ['id' => 1],
            [
                'business_name' => 'Clínica Demo',
                'ruc' => '00000000000',
                'address' => 'Dirección pendiente',
                'phone' => '999999999',
                'email' => 'contacto@clinica.test',
                'primary_color' => '#0F766E',
                'secondary_color' => '#111827',
                'footer_text' => 'Documento generado automáticamente por el sistema.',
                'show_qr' => false,
                'show_signature' => false,
                'show_cmp' => true,
            ]
        );
    }
}
