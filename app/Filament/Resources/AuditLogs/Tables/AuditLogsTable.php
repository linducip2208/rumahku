<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime('d M Y H:i')->sortable()->label('Waktu'),
                TextColumn::make('user.name')->searchable()->label('Pengguna'),
                TextColumn::make('action')->badge()->sortable()->label('Aksi'),
                TextColumn::make('auditable_type')->searchable()->label('Model'),
                TextColumn::make('auditable_id')->sortable()->label('ID'),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ])
                    ->label('Aksi'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
