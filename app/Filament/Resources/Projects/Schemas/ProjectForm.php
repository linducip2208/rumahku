<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
            Textarea::make('facilities')
                ->label('Fasilitas')
                ->placeholder('One Gate System, Taman Bermain, Masjid, Minimarket')
                ->columnSpanFull(),
            Repeater::make('unit_types')
                ->label('Tipe Unit')
                ->schema([
                    TextInput::make('nama')->required()->maxLength(100)->label('Nama Tipe'),
                    TextInput::make('lt')->numeric()->minValue(0)->label('LT (m²)'),
                    TextInput::make('lb')->numeric()->minValue(0)->label('LB (m²)'),
                    TextInput::make('kt')->numeric()->minValue(0)->label('KT'),
                    TextInput::make('km')->numeric()->minValue(0)->label('KM'),
                    TextInput::make('harga')->numeric()->minValue(0)->prefix('Rp')->label('Harga'),
                ])
                ->columns(3)
                ->columnSpanFull()
                ->addActionLabel('Tambah tipe unit'),
            RichEditor::make('description')->columnSpanFull()->label('Deskripsi'),
        ]);
    }
}
