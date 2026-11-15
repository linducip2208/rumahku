<?php

namespace App\Filament\Resources\Articles\Tables;

use App\Models\Article;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable()->limit(50)->label('Judul'),
                TextColumn::make('type')->badge()->sortable()->label('Tipe'),
                TextColumn::make('city')->sortable()->label('Kota'),
                TextColumn::make('updated_at')->dateTime('d M Y')->sortable()->label('Diubah'),
            ])
            ->filters([
                SelectFilter::make('type')->options([
                    'rumah' => 'Rumah',
                    'apartemen' => 'Apartemen',
                    'tanah' => 'Tanah',
                    'ruko' => 'Ruko',
                    'villa' => 'Villa',
                ])->label('Tipe'),
                SelectFilter::make('city')
                    ->options(fn (): array => Article::query()
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
