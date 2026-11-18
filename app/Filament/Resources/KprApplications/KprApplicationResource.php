<?php

namespace App\Filament\Resources\KprApplications;

use App\Filament\Resources\KprApplications\Pages\CreateKprApplication;
use App\Filament\Resources\KprApplications\Pages\EditKprApplication;
use App\Filament\Resources\KprApplications\Pages\ListKprApplications;
use App\Filament\Resources\KprApplications\Pages\ViewKprApplication;
use App\Filament\Resources\KprApplications\Schemas\KprApplicationForm;
use App\Filament\Resources\KprApplications\Schemas\KprApplicationInfolist;
use App\Filament\Resources\KprApplications\Tables\KprApplicationsTable;
use App\Models\KprApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KprApplicationResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?string $model = KprApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Pengajuan KPR';

    protected static ?string $modelLabel = 'Pengajuan KPR';

    protected static ?string $pluralModelLabel = 'Pengajuan KPR';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return KprApplicationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KprApplicationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KprApplicationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKprApplications::route('/'),
            'create' => CreateKprApplication::route('/create'),
            'view' => ViewKprApplication::route('/{record}'),
            'edit' => EditKprApplication::route('/{record}/edit'),
        ];
    }
}
