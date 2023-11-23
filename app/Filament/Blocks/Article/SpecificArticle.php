<?php

namespace App\Filament\Blocks\Article;

use App\Filament\Fields\LayoutField;
use App\Models\Article;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class SpecificArticle
{
    public static function make()
    {
        return Block::make('specific_article')
            ->schema([
                TextInput::make('title')
                    ->required(),
                Select::make('Articles')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Article::where('title', 'like', "%{$search}%")->limit(50)->pluck('title', 'id')->toArray()),
                LayoutField::make('layout')
            ]);
    }
}
