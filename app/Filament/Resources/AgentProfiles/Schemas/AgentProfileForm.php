<?php

namespace App\Filament\Resources\AgentProfiles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AgentProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->unique(ignoreRecord: true)
                ->label('User'),
            TextInput::make('agency')->maxLength(255)->label('Agensi'),
            TextInput::make('phone')->tel()->required()->maxLength(30)->regex('/^(\+62|62|0)[0-9]{8,14}$/')->validationMessages(['regex' => 'Format telepon tidak valid.'])->label('Telepon'),
            TextInput::make('wa')->tel()->required()->maxLength(30)->regex('/^(\+62|62|0)[0-9]{8,14}$/')->validationMessages(['regex' => 'Format WA tidak valid.'])->label('WhatsApp'),
            FileUpload::make('photo')->image()->directory('agents')->label('Foto'),
            DateTimePicker::make('verified_at')->label('Terverifikasi Pada'),
        ]);
    }
}
