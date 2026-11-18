<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama'),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'agent' => 'Agen',
                        'owner' => 'Owner',
                        'user' => 'User',
                    ])
                    ->required()
                    ->default('user')
                    ->label('Role'),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn ($state): bool => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255)
                    ->label('Password (kosongkan jika tidak diubah)'),
            ]);
    }
}
