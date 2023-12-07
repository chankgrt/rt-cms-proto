<?php

namespace App\Filament\Resources\CustomMediaResource\Pages;

use App\Filament\Resources\CustomMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomMedia extends ViewRecord
{
    protected static string $resource = CustomMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
