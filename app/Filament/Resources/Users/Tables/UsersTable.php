<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\Role;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Usuario')
                    ->description(fn ($record): string => $record->email)
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->icon(Heroicon::OutlinedPhone)
                    ->searchable()
                    ->placeholder('No registrado'),

                TextColumn::make('role.name')
                    ->label('Rol')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::roleLabels()[$state] ?? ucfirst((string) $state))
                    ->color(fn (?string $state): string => self::roleColors()[$state] ?? 'gray')
                    ->sortable(),

                TextColumn::make('profile_type')
                    ->label('Perfil')
                    ->state(fn ($record): string => self::profileType($record))
                    ->badge()
                    ->color(fn ($record): string => self::profileColors()[self::profileType($record)] ?? 'gray'),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Registro')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role_id')
                    ->label('Rol')
                    ->options(fn () => Role::query()
                        ->whereIn('name', ['admin', 'doctor', 'receptionist', 'patient'])
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->map(fn (string $role): string => self::roleLabels()[$role] ?? ucfirst($role))
                        ->all()),

                SelectFilter::make('is_active')
                    ->label('Estado')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver')
                    ->icon(Heroicon::OutlinedDocumentMagnifyingGlass),
                EditAction::make()
                    ->label('Editar')
                    ->icon(Heroicon::OutlinedPencilSquare),
            ])
            ->toolbarActions([]);
    }

    private static function profileType($record): string
    {
        if ($record->doctor) {
            return 'Médico';
        }

        if ($record->patient) {
            return 'Paciente';
        }

        return self::roleLabels()[$record->role?->name] ?? 'Interno';
    }

    private static function roleLabels(): array
    {
        return [
            'admin' => 'Admin',
            'doctor' => 'Médico',
            'receptionist' => 'Recepcionista',
            'patient' => 'Paciente',
        ];
    }

    private static function roleColors(): array
    {
        return [
            'admin' => 'danger',
            'doctor' => 'info',
            'receptionist' => 'warning',
            'patient' => 'success',
        ];
    }

    private static function profileColors(): array
    {
        return [
            'Admin' => 'danger',
            'Médico' => 'info',
            'Recepcionista' => 'warning',
            'Paciente' => 'success',
            'Interno' => 'gray',
        ];
    }
}
