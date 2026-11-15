<?php

namespace App\Filament\Resources\KprApplications\Pages;

use App\Filament\Resources\KprApplications\KprApplicationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKprApplications extends ListRecords
{
    protected static string $resource = KprApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
