<?php

namespace App\Filament\Resources\Article;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Article\DossierResource\Pages;
use App\Filament\Resources\Article\DossierResource\RelationManagers;
use App\Models\Article;
use App\Models\Article\Dossier;
use App\Models\ArticleDossier;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class DossierResource extends Resource
{
    protected static ?string $model = ArticleDossier::class;
    protected static ?int $navigationSort = 3;
    protected static ?string $label = 'Dossier';

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Articles->getLabel();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Media')
                            ->description('Article Media')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('image'),
                                TextInput::make('title')
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        if (($get('slug') ?? '') !== Str::slug($old)) {
                                            return;
                                        }
                                        $set('slug', Str::slug($state));
                                    }),
                                TextInput::make('meta_title'),
                                TextInput::make('description'),
                                TextInput::make('slug')->readOnly(),
                                Checkbox::make('highlight_on_frontpage')
                            ])
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('meta_title'),
                TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDossiers::route('/'),
            'create' => Pages\CreateDossier::route('/create'),
            'view' => Pages\ViewDossier::route('/{record}'),
            'edit' => Pages\EditDossier::route('/{record}/edit'),
        ];
    }
}
