<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
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
                TextColumn::make('status')->badge()->sortable()->label('Status'),
                TextColumn::make('follow_up_at')->dateTime('d M Y H:i')->sortable()->toggleable()->label('Follow Up'),
                TextColumn::make('created_at')->dateTime('d M Y')->sortable()->label('Masuk'),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->options(fn (): array => Lead::query()
                        ->distinct()
                        ->pluck('source', 'source')
                        ->toArray())
                    ->label('Sumber'),
                SelectFilter::make('status')
                    ->options([
                        'baru' => 'Baru',
                        'dihubungi' => 'Dihubungi',
                        'survei' => 'Survei',
                        'negosiasi' => 'Negosiasi',
                        'closing' => 'Closing',
                        'gagal' => 'Gagal',
                    ])
                    ->label('Status'),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('changeStatus')
                    ->label('Ubah Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'baru' => 'Baru',
                                'dihubungi' => 'Dihubungi',
                                'survei' => 'Survei',
                                'negosiasi' => 'Negosiasi',
                                'closing' => 'Closing',
                                'gagal' => 'Gagal',
                            ])
                            ->default(fn (Lead $record): string => $record->status)
                            ->required()
                            ->label('Status'),
                        DateTimePicker::make('follow_up_at')
                            ->default(fn (Lead $record) => $record->follow_up_at)
                            ->label('Follow Up'),
                    ])
                    ->action(function (Lead $record, array $data): void {
                        $record->update([
                            'status' => $data['status'],
                            'follow_up_at' => $data['follow_up_at'] ?? null,
                        ]);
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
