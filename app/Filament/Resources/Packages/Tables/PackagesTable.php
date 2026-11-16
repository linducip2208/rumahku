<?php

namespace App\Filament\Resources\Packages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->label('Nama'),
                TextColumn::make('slug')->searchable()->sortable()->label('Slug'),
                TextColumn::make('price')->money('IDR')->sortable()->label('Harga'),
                TextColumn::make('duration_days')->suffix(' hari')->sortable()->label('Durasi'),
                TextColumn::make('max_photos')->sortable()->label('Maks. Foto'),
                TextColumn::make('max_listings')->sortable()->label('Maks. Listing')
                    ->placeholder('Tanpa batas'),
                IconColumn::make('featured_included')->boolean()->label('Featured'),
                TextColumn::make('badge')->badge()->toggleable()->label('Badge'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
