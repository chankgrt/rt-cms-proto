<?php

namespace App\Filament\Resources\Article\DossierResource\Pages;

use App\Filament\Resources\Article\DossierResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDossier extends ViewRecord
{
    protected static string $resource = DossierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
