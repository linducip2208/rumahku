<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->limit(40)->label('Proyek'),
                TextColumn::make('developer.name')->sortable()->toggleable()->label('Developer'),
                TextColumn::make('city')->sortable()->label('Kota'),
                TextColumn::make('price_from')->money('IDR')->sortable()->label('Harga Dari'),
            ])
            ->filters([
                SelectFilter::make('city')
                    ->options(fn (): array => Project::query()
                        ->distinct()
                        ->whereNotNull('city')
                        ->pluck('city', 'city')
                        ->toArray())
                    ->label('Kota'),
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
