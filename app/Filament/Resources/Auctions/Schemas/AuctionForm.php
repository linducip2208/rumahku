<?php

namespace App\Filament\Resources\Auctions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuctionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Lelang')->schema([
                Select::make('property_id')
                    ->relationship('property', 'title')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Properti'),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'open' => 'Open',
                        'closed' => 'Closed',
                    ])
                    ->required()
                    ->default('draft'),
                TextInput::make('start_price')->numeric()->prefix('Rp')->minValue(0)->required()->label('Harga Awal'),
                TextInput::make('current_price')->numeric()->prefix('Rp')->minValue(0)->label('Harga Saat Ini'),
                DateTimePicker::make('starts_at')->label('Mulai'),
                DateTimePicker::make('ends_at')->label('Berakhir'),
            ])->columns(2),
        ]);
    }
}
