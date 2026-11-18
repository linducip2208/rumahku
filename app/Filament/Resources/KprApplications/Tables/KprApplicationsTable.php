<?php

namespace App\Filament\Resources\KprApplications\Tables;

use App\Models\KprApplication;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class KprApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->label('Nama'),
                TextColumn::make('property.title')->searchable()->sortable()->limit(30)->label('Properti'),
                TextColumn::make('phone')->searchable()->label('Telepon'),
                TextColumn::make('dp_amount')->money('IDR')->sortable()->label('DP'),
                TextColumn::make('tenor_months')->suffix(' bln')->sortable()->label('Tenor'),
                TextColumn::make('bank_preference')->badge()->sortable()->label('Bank'),
                TextColumn::make('status')->badge()->sortable()->label('Status'),
                TextColumn::make('created_at')->dateTime('d M Y')->sortable()->label('Masuk'),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'contacted' => 'Contacted',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->label('Status'),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('setStatus')
                    ->label('Ubah Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'contacted' => 'Contacted',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default(fn (KprApplication $record): string => $record->status)
                            ->required()
                            ->label('Status'),
                    ])
                    ->action(function (KprApplication $record, array $data): void {
                        $record->update(['status' => $data['status']]);
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
