<?php

namespace App\Filament\Blocks\Article;

use App\Filament\Fields\LayoutField;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class ContentBlock
{
    public static function make()
    {
        return Block::make('content_block')
            ->schema([
                Textarea::make('content')
                    ->required(),
                LayoutField::make(name:'layout')
            ]);
    }
}
