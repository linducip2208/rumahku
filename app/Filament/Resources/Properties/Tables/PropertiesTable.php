<?php

namespace App\Filament\Resources\Properties\Tables;

use App\Models\Property;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PropertiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable()->limit(40),
                TextColumn::make('type')->badge()->sortable(),
                TextColumn::make('listing_status')->badge()->sortable()->label('Status'),
                TextColumn::make('price')->money('IDR')->sortable(),
                TextColumn::make('city')->sortable()->toggleable(),
                IconColumn::make('featured')->boolean()->label('Featured'),
                TextColumn::make('published_at')->dateTime('d M Y')->sortable()->label('Published'),
                TextColumn::make('sold_at')->dateTime('d M Y')->sortable()->label('Terjual'),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status_sold')
                    ->label('Status Sold')
                    ->options([
                        'aktif' => 'Aktif',
                        'terjual' => 'Terjual',
                        'draft' => 'Draft',
                    ])
                    ->query(function ($query, array $data): void {
                        $value = $data['value'] ?? null;

                        match ($value) {
                            'aktif' => $query->whereNotNull('published_at')->whereNull('sold_at'),
                            'terjual' => $query->whereNotNull('sold_at'),
                            'draft' => $query->whereNull('published_at'),
                            default => null,
                        };
                    }),
                SelectFilter::make('type')->options([
                    'rumah' => 'Rumah',
                    'apartemen' => 'Apartemen',
                    'tanah' => 'Tanah',
                    'ruko' => 'Ruko',
                    'villa' => 'Villa',
                ]),
                SelectFilter::make('listing_status')->options([
                    'jual' => 'Dijual',
                    'sewa' => 'Disewa',
                ])->label('Status'),
                SelectFilter::make('city')->options(fn (): array => Property::query()
                    ->distinct()
                    ->whereNotNull('city')
                    ->pluck('city', 'city')
                    ->toArray())->label('Kota'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('publish')
                        ->label('Publish')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $count = 0;

                            foreach ($records as $record) {
                                if ($record->published_at === null) {
                                    $record->update(['published_at' => now()]);
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title("{$count} properti dipublish.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('unpublish')
                        ->label('Unpublish')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->color('danger')
                        ->action(function (Collection $records): void {
                            $count = 0;

                            foreach ($records as $record) {
                                if ($record->published_at !== null) {
                                    $record->update(['published_at' => null]);
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title("{$count} properti di-unpublish.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    BulkAction::make('mark_sold')
                        ->label('Tandai Terjual')
                        ->icon('heroicon-o-banknotes')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $count = 0;

                            foreach ($records as $record) {
                                if ($record->sold_at === null) {
                                    $record->update(['sold_at' => now()]);
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title("{$count} properti ditandai terjual.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('cancel_sold')
                        ->label('Batalkan Terjual')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->requiresConfirmation()
                        ->color('warning')
                        ->action(function (Collection $records): void {
                            $count = 0;

                            foreach ($records as $record) {
                                if ($record->sold_at !== null) {
                                    $record->update(['sold_at' => null]);
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title("{$count} properti dibatalkan status terjualnya.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
