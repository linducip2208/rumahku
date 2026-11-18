<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255)->columnSpanFull()->label('Judul')
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug((string) $state))),
            TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
            Select::make('type')
                ->options([
                    'rumah' => 'Rumah',
                    'apartemen' => 'Apartemen',
                    'tanah' => 'Tanah',
                    'ruko' => 'Ruko',
                    'villa' => 'Villa',
                ])
                ->label('Tipe Properti (pSEO)'),
            TextInput::make('city')->maxLength(255)->label('Kota'),
            RichEditor::make('body')->columnSpanFull()->required()->label('Isi'),
        ]);
    }
}
