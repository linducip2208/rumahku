<?php

namespace App\Filament\Resources\PropertyOrders\Pages;

use App\Filament\Resources\PropertyOrders\PropertyOrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPropertyOrders extends ListRecords
{
    protected static string $resource = PropertyOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
