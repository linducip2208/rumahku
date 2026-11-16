<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->label('Nama'),
                TextColumn::make('phone')->searchable()->label('Telepon'),
                TextColumn::make('email')->searchable()->toggleable()->label('Email'),
                TextColumn::make('subject')->searchable()->limit(30)->toggleable()->label('Subjek'),
                TextColumn::make('message')->searchable()->limit(60)->label('Pesan'),
                IconColumn::make('is_read')->boolean()->sortable()->label('Dibaca'),
                TextColumn::make('created_at')->dateTime('d M Y H:i')->sortable()->label('Masuk'),
            ])
            ->filters([
                SelectFilter::make('is_read')
                    ->options(['1' => 'Sudah dibaca', '0' => 'Belum dibaca'])
                    ->label('Status baca'),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('markAsRead')
                    ->label('Tandai dibaca')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn ($record): bool => ! $record->is_read)
                    ->action(fn ($record): bool => $record->update(['is_read' => true])),
            ]);
    }
}
