<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Audit')->schema([
                TextEntry::make('created_at')->dateTime('d M Y H:i')->label('Waktu'),
                TextEntry::make('user.name')->label('Pengguna'),
                TextEntry::make('action')->badge()->label('Aksi'),
                TextEntry::make('auditable_type')->label('Model'),
                TextEntry::make('auditable_id')->label('ID'),
                TextEntry::make('ip')->label('IP'),
                TextEntry::make('old_values')->json()->columnSpanFull()->label('Nilai lama'),
                TextEntry::make('new_values')->json()->columnSpanFull()->label('Nilai baru'),
            ])->columns(2),
        ]);
    }
}
