<?php

namespace App\Filament\Resources\KprApplications\Pages;

use App\Filament\Resources\KprApplications\KprApplicationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKprApplication extends EditRecord
{
    protected static string $resource = KprApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
