<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PermissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Permission')->schema([
                TextEntry::make('name')->label('Nama'),
                TextEntry::make('guard_name')->badge()->label('Guard'),
                TextEntry::make('roles.name')
                    ->badge()
                    ->label('Dipakai Role'),
                TextEntry::make('updated_at')->dateTime('d M Y H:i')->label('Diubah'),
            ])->columns(2),
        ]);
    }
}
