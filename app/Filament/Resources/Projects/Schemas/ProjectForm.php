<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('developer_user_id')
                ->relationship('developer', 'name')
                ->searchable()
                ->preload()
                ->label('Developer'),
            TextInput::make('name')->required()->maxLength(255)->label('Nama Proyek'),
            TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
            TextInput::make('city')->required()->maxLength(255)->label('Kota'),
            TextInput::make('price_from')->numeric()->prefix('Rp')->required()->label('Harga Dari'),
            RichEditor::make('description')->columnSpanFull()->label('Deskripsi'),
        ]);
    }
}
