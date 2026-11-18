<?php

namespace App\Filament\Resources\PropertyOrders;

use App\Filament\Resources\PropertyOrders\Pages\CreatePropertyOrder;
use App\Filament\Resources\PropertyOrders\Pages\EditPropertyOrder;
use App\Filament\Resources\PropertyOrders\Pages\ListPropertyOrders;
use App\Filament\Resources\PropertyOrders\Schemas\PropertyOrderForm;
use App\Filament\Resources\PropertyOrders\Tables\PropertyOrdersTable;
use App\Models\PropertyOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PropertyOrderResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?string $model = PropertyOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $recordTitleAttribute = 'package_slug';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()->where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return PropertyOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PropertyOrdersTable::configure($table);
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
            'index' => ListPropertyOrders::route('/'),
            'create' => CreatePropertyOrder::route('/create'),
            'edit' => EditPropertyOrder::route('/{record}/edit'),
        ];
    }
}
