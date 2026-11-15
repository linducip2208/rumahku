<?php

namespace App\Filament\Resources\PropertyOrders\Pages;

use App\Filament\Resources\PropertyOrders\PropertyOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPropertyOrder extends EditRecord
{
    protected static string $resource = PropertyOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
