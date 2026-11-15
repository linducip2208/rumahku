<?php

namespace App\Filament\Resources\KprApplications\Schemas;

use App\Models\KprApplication;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KprApplicationForm
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
            Select::make('user_id')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->label('Pengguna'),
            TextInput::make('name')->required()->maxLength(255)->label('Nama'),
            TextInput::make('phone')->tel()->required()->maxLength(30)->label('Telepon'),
            TextInput::make('email')->email()->maxLength(255)->label('Email'),
            TextInput::make('dp_amount')->numeric()->required()->minValue(0)->prefix('Rp')->label('Uang Muka (DP)'),
            Select::make('tenor_months')
                ->options(array_combine(KprApplication::TENORS, array_map(fn (int $t): string => $t.' bulan', KprApplication::TENORS)))
                ->required()
                ->label('Tenor'),
            Select::make('bank_preference')
                ->options([
                    'mandiri' => 'Mandiri',
                    'bca' => 'BCA',
                    'bri' => 'BRI',
                    'bni' => 'BNI',
                    'syariah' => 'Syariah',
                ])
                ->label('Bank Preferensi'),
            TextInput::make('monthly_income')->numeric()->minValue(0)->prefix('Rp')->label('Penghasilan Bulanan'),
            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'contacted' => 'Contacted',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->default('pending')
                ->required()
                ->label('Status'),
            Textarea::make('notes')->columnSpanFull()->label('Catatan'),
        ]);
    }
}
