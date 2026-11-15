<?php

namespace App\Filament\Resources\AuctionBids\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuctionBidsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('auction.id')->sortable()->label('Lelang'),
                TextColumn::make('auction.property.title')->searchable()->limit(40)->label('Properti'),
                TextColumn::make('user.name')->searchable()->label('Penawar'),
                TextColumn::make('amount')->money('IDR')->sortable()->label('Nominal'),
                TextColumn::make('created_at')->dateTime('d M Y H:i')->sortable()->label('Waktu'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
