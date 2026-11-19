<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug((string) $state)))
                    ->label('Nama'),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Slug'),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->required()
                    ->label('Harga (IDR)'),
                TextInput::make('duration_days')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->suffix('hari')
                    ->label('Durasi'),
                TextInput::make('max_photos')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->label('Maks. Foto'),
                TextInput::make('max_listings')
                    ->numeric()
                    ->minValue(1)
                    ->nullable()
                    ->label('Maks. Listing Aktif')
                    ->helperText('Kosongkan untuk tanpa batas.'),
                Toggle::make('featured_included')
                    ->label('Termasuk Featured'),
                TextInput::make('badge')
                    ->maxLength(50)
                    ->label('Badge'),
            ]);
    }
}
