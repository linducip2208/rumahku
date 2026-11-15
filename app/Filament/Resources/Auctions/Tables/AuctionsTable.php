<?php

namespace App\Filament\Resources\Auctions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuctionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('property.title')->searchable()->limit(40)->label('Properti'),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('start_price')->money('IDR')->sortable()->label('Harga Awal'),
                TextColumn::make('current_price')->money('IDR')->sortable()->label('Harga Saat Ini'),
                TextColumn::make('ends_at')->dateTime('d M Y H:i')->sortable()->label('Berakhir'),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'open' => 'Open',
                    'closed' => 'Closed',
                ]),
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
