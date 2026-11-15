<?php

namespace App\Filament\Resources\KprApplications\Pages;

use App\Filament\Resources\KprApplications\KprApplicationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKprApplication extends ViewRecord
{
    protected static string $resource = KprApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
