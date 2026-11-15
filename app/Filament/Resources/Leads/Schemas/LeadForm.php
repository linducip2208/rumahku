<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('property_id')
                ->relationship('property', 'title')
                ->searchable()
                ->preload()
                ->required()
                ->label('Properti'),
            Select::make('agent_user_id')
                ->relationship('agent', 'name')
                ->searchable()
                ->preload()
                ->label('Agen'),
            TextInput::make('name')->required()->maxLength(255)->label('Nama'),
            TextInput::make('phone')->tel()->required()->maxLength(30)->label('Telepon'),
            Textarea::make('message')->columnSpanFull()->label('Pesan'),
            TextInput::make('source')->maxLength(100)->default('web')->label('Sumber'),
        ]);
    }
}
