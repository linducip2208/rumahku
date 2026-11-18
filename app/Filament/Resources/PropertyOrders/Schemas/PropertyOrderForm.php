<?php

namespace App\Filament\Resources\PropertyOrders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PropertyOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Pengguna'),
                Select::make('property_id')
                    ->relationship('property', 'title')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Properti'),
                Select::make('package_slug')
                    ->relationship('package', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Paket'),
                TextInput::make('amount')
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->required()
                    ->label('Nominal (IDR)'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'expired' => 'Expired',
                    ])
                    ->required()
                    ->default('pending')
                    ->label('Status'),
                DateTimePicker::make('paid_at')->label('Dibayar Pada'),
                DateTimePicker::make('expires_at')->label('Kedaluwarsa Pada'),
            ]);
    }
}
