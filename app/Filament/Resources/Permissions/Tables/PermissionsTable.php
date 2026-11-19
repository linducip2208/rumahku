<?php

namespace App\Filament\Resources\Permissions\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Permission'),
                TextColumn::make('roles_count')
                    ->counts('roles')
                    ->sortable()
                    ->label('Role'),
                TextColumn::make('users_count')
                    ->counts('users')
                    ->sortable()
                    ->label('Pengguna'),
                TextColumn::make('guard_name')
                    ->badge()
                    ->toggleable()
                    ->label('Guard'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
