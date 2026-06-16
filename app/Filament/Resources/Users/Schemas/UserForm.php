<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del usuario')
                    ->description('Cuenta administrativa o interna registrada en el sistema.')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->rule(function ($record) {
                                return Rule::unique('users', 'email')
                                    ->ignore($record?->id);
                            })
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),

                        Select::make('role_id')
                            ->label('Rol')
                            ->options(fn () => Role::query()
                                ->whereIn('name', ['admin', 'doctor', 'receptionist', 'patient'])
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->map(fn (string $role): string => self::roleLabels()[$role] ?? ucfirst($role))
                                ->all())
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                Section::make('Acceso')
                    ->description('Control básico de acceso al panel y al sistema.')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->columns(2)
                    ->schema([
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->maxLength(255),

                        Toggle::make('is_active')
                            ->label('Usuario activo')
                            ->default(true),
                    ]),
            ]);
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
}
