<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\MedicalDocument;
use App\Models\Patient;
use App\Models\Prescription;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClinicStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pacientes registrados', Patient::count())
                ->description('Total de pacientes en la clínica')
                ->icon('heroicon-o-users'),

            Stat::make('Médicos activos', Doctor::count())
                ->description('Profesionales registrados')
                ->icon('heroicon-o-user-group'),

            Stat::make('Citas de hoy', Appointment::whereDate('scheduled_at', today())->count())
                ->description('Agenda médica del día')
                ->icon('heroicon-o-calendar-days'),

            Stat::make('Consultas médicas', Consultation::count())
                ->description('Consultas registradas')
                ->icon('heroicon-o-clipboard-document-list'),

            Stat::make('Recetas emitidas', Prescription::count())
                ->description('Recetas electrónicas generadas')
                ->icon('heroicon-o-document-text'),

            Stat::make('Documentos médicos', MedicalDocument::count())
                ->description('Archivos clínicos subidos')
                ->icon('heroicon-o-folder'),
        ];
    }
}
