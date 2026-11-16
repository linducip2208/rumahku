<?php

namespace App\Filament\Resources\Properties\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PropertyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dasar')->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Agen / Pemilik'),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('type')
                    ->options([
                        'rumah' => 'Rumah',
                        'apartemen' => 'Apartemen',
                        'tanah' => 'Tanah',
                        'ruko' => 'Ruko',
                        'villa' => 'Villa',
                    ])
                    ->required(),
                Select::make('listing_status')
                    ->options([
                        'jual' => 'Dijual',
                        'sewa' => 'Disewa',
                    ])
                    ->required()
                    ->label('Status Listing'),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->required(),
            ])->columns(2),
            Section::make('Spesifikasi')->schema([
                TextInput::make('bedrooms')->numeric()->minValue(0)->label('Kamar Tidur'),
                TextInput::make('bathrooms')->numeric()->minValue(0)->label('Kamar Mandi'),
                TextInput::make('land_area')->numeric()->minValue(0)->suffix('m²')->label('Luas Tanah'),
                TextInput::make('building_area')->numeric()->minValue(0)->suffix('m²')->label('Luas Bangunan'),
                Select::make('certificate')
                    ->options([
                        'shm' => 'SHM',
                        'hgb' => 'HGB',
                        'girik' => 'Girik',
                        'ajb' => 'AJB',
                        'strata' => 'Strata Title',
                    ])
                    ->label('Sertifikat'),
                TagsInput::make('facilities')
                    ->label('Fasilitas')
                    ->placeholder('Ketik fasilitas lalu Enter')
                    ->suggestions([
                        'Carport',
                        'Taman',
                        'Keamanan 24 Jam',
                        'PAM',
                        'Listrik 2200W',
                        'Kolam Renang',
                        'Gym',
                        'Lift',
                        'Parkir Basement',
                        'Akses Jalan',
                        'Sertifikat Pecah',
                        'Air Bersih',
                        'Parkir Luas',
                        'Rolling Door',
                        'Toilet',
                        'Kantor Lantai 2',
                        'Private Pool',
                        'Taman Tropis',
                        'Gazebo',
                        'Keamanan',
                    ])
                    ->columnSpanFull(),
            ])->columns(3),
            Section::make('Lokasi')->schema([
                TextInput::make('province')->required()->maxLength(100)->label('Provinsi'),
                TextInput::make('city')->required()->maxLength(100)->label('Kota'),
                TextInput::make('district')->maxLength(100)->label('Kecamatan'),
                Textarea::make('address')->required()->columnSpanFull()->label('Alamat'),
                TextInput::make('lat')->numeric()->label('Latitude'),
                TextInput::make('lng')->numeric()->label('Longitude'),
            ])->columns(3),
            Section::make('Konten & Publikasi')->schema([
                RichEditor::make('description')->required()->columnSpanFull()->label('Deskripsi'),
                Toggle::make('featured')->label('Featured'),
                DateTimePicker::make('published_at')->label('Dipublikasikan Pada'),
            ])->columns(2),
        ]);
    }
}
