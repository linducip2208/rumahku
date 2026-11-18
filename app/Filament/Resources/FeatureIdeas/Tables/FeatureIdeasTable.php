<?php

namespace App\Filament\Resources\FeatureIdeas\Tables;

use App\Services\Ideas\IdeaBank;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FeatureIdeasTable
{
    public static function configure(Table $table): Table
    {
        $features = [];
        foreach (IdeaBank::FEATURES as $key => $label) {
            $features[$key] = $label;
        }

        return $table
            ->columns([
                TextColumn::make('slot')->sortable()->label('#'),
                TextColumn::make('title')->searchable()->limit(60)->label('Ide'),
                TextColumn::make('priority')->badge()->sortable()->label('Prioritas'),
                TextColumn::make('mechanism')->badge()->label('Mekanisme'),
                TextColumn::make('impact')->badge()->sortable()->label('Dampak'),
                TextColumn::make('effort')->badge()->sortable()->label('Usaha'),
                TextColumn::make('status')->badge()->label('Status'),
            ])
            ->filters([
                SelectFilter::make('feature_key')
                    ->label('Fitur')
                    ->options($features),
                SelectFilter::make('priority')
                    ->label('Prioritas')
                    ->options(['P0' => 'P0', 'P1' => 'P1', 'P2' => 'P2', 'P3' => 'P3', 'P4' => 'P4']),
                SelectFilter::make('mechanism')
                    ->label('Mekanisme')
                    ->options(array_combine(IdeaBank::MECHANISMS, IdeaBank::MECHANISMS)),
                SelectFilter::make('status')
                    ->options(['backlog' => 'Backlog', 'planned' => 'Planned', 'done' => 'Done']),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('setStatus')
                    ->label('Status')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Select::make('status')
                            ->options(['backlog' => 'Backlog', 'planned' => 'Planned', 'done' => 'Done'])
                            ->required()
                            ->default(fn ($record) => $record->status),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->update(['status' => $data['status']]);
                    }),
            ]);
    }
}
