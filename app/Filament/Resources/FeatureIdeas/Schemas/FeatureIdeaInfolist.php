<?php

namespace App\Filament\Resources\FeatureIdeas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FeatureIdeaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('title')->label('Ide'),
            TextEntry::make('description')->label('Deskripsi')->columnSpanFull(),
            TextEntry::make('audience')->label('Audiens'),
            TextEntry::make('need')->label('Kebutuhan'),
            TextEntry::make('mechanism')->label('Mekanisme'),
            TextEntry::make('impact')->badge()->label('Dampak'),
            TextEntry::make('effort')->badge()->label('Usaha'),
            TextEntry::make('status')->badge()->label('Status'),
        ]);
    }
}
