<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomMediaResource\Pages;
use App\Filament\Resources\CustomMediaResource\RelationManagers;
use App\Models\CustomMedia;
use App\Models\Media;
use Awcodes\Curator\Resources\MediaResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CustomMediaResource extends MediaResource
{
    public static function getNavigationLabel(): string
    {
        return 'Custom Media';
    }

    public static function form(Form $form): Form
    {
        // $form->get
        return parent::form($form);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => MediaResource\ListMedia::route('/'),
            'create' => Pages\CreateCustomMedia::route('/creates'),
            'view' => Pages\ViewCustomMedia::route('/{record}'),
            'edit' => Pages\EditCustomMedia::route('/{record}/edit'),
        ];
    }
}
