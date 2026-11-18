<?php

namespace App\Filament\Resources\PropertyOrders\Tables;

use App\Models\PropertyOrder;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PropertyOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->searchable()->sortable()->label('Pengguna'),
                TextColumn::make('property.title')->searchable()->sortable()->limit(30)->label('Properti'),
                TextColumn::make('package_slug')->badge()->sortable()->label('Paket'),
                TextColumn::make('amount')->money('IDR')->sortable()->label('Nominal'),
                TextColumn::make('status')->badge()->sortable()->label('Status'),
                TextColumn::make('paid_at')->dateTime('d M Y H:i')->sortable()->toggleable()->label('Dibayar'),
                TextColumn::make('expires_at')->dateTime('d M Y')->sortable()->toggleable()->label('Kedaluwarsa'),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'expired' => 'Expired',
                    ])
                    ->label('Status'),
            ])
            ->recordActions([
                Action::make('markPaid')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (PropertyOrder $record): bool => $record->status === 'pending')
                    ->action(function (PropertyOrder $record): void {
                        $record->markAsPaid();
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
