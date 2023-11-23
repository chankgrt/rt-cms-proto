<?php

namespace App\Filament\Fields;

use Filament\Forms\Components\Select;

class LayoutField
{
    public static function make(string $name, ?string $label = null)
    {
        return Select::make($name)
            ->default('full')
            ->label($label)
            ->options([
                'full' => 'Full Width (Default)',
                'half' => 'One Half 1/2',
                'third' => 'One Third 1/3',
                'fourth' => 'One Fourth 1/4'
            ]);
    }
}
