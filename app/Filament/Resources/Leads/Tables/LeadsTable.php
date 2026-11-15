<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Models\Lead;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->label('Nama'),
                TextColumn::make('property.title')->searchable()->sortable()->limit(30)->label('Properti'),
                TextColumn::make('agent.name')->sortable()->toggleable()->label('Agen'),
                TextColumn::make('phone')->searchable()->label('Telepon'),
                TextColumn::make('source')->badge()->label('Sumber'),
                TextColumn::make('created_at')->dateTime('d M Y')->sortable()->label('Masuk'),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->options(fn (): array => Lead::query()
                        ->distinct()
                        ->pluck('source', 'source')
                        ->toArray())
                    ->label('Sumber'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
