<?php

namespace App\Filament\Resources\AgentProfiles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AgentProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')->circular()->label('Foto'),
                TextColumn::make('user.name')->searchable()->sortable()->label('Nama'),
                TextColumn::make('agency')->searchable()->sortable()->label('Agensi'),
                TextColumn::make('phone')->searchable()->label('Telepon'),
                TextColumn::make('wa')->searchable()->label('WhatsApp'),
                TextColumn::make('verified_at')->dateTime('d M Y')->sortable()->label('Verified'),
            ])
            ->filters([
                TernaryFilter::make('verified_at')
                    ->label('Terverifikasi')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('verified_at'),
                        false: fn ($query) => $query->whereNull('verified_at'),
                    ),
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
