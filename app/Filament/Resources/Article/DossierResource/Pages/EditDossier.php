<?php

namespace App\Filament\Resources\Article\DossierResource\Pages;

use App\Filament\Resources\Article\DossierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDossier extends EditRecord
{
    protected static string $resource = DossierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
