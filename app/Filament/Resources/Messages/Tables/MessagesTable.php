<?php

namespace App\Filament\Resources\Messages\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->label('#'),
                TextColumn::make('property.title')->searchable()->limit(40)->label('Properti'),
                TextColumn::make('sender.name')->searchable()->label('Pengirim'),
                TextColumn::make('body')->searchable()->limit(60)->label('Pesan'),
                TextColumn::make('created_at')->dateTime('d M Y H:i')->sortable()->label('Waktu'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
