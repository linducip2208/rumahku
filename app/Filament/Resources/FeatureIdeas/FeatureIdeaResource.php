<?php

namespace App\Filament\Resources\FeatureIdeas;

use App\Filament\Resources\FeatureIdeas\Pages\ListFeatureIdeas;
use App\Filament\Resources\FeatureIdeas\Pages\ViewFeatureIdea;
use App\Filament\Resources\FeatureIdeas\Schemas\FeatureIdeaInfolist;
use App\Filament\Resources\FeatureIdeas\Tables\FeatureIdeasTable;
use App\Models\FeatureIdea;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FeatureIdeaResource extends Resource
{
    protected static ?string $model = FeatureIdea::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    protected static ?string $recordTitleAttribute = 'title';

    public static function infolist(Schema $schema): Schema
    {
        return FeatureIdeaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FeatureIdeasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFeatureIdeas::route('/'),
            'view' => ViewFeatureIdea::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
