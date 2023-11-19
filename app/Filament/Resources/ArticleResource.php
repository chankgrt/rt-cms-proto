<?php

namespace App\Filament\Resources;

use App\Enums\NavigationGroup;
use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\RelationManagers;
use App\Models\Article;
use App\Models\ArticleCategory;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return NavigationGroup::Articles->getLabel();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Media')
                            ->description('Article Media')
                            ->schema([
                                Forms\Components\SpatieMediaLibraryFileUpload::make('article'),
                            ])
                    ])
                    ->columnSpanFull(),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Basic')
                            ->description('Basic article information')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                MarkdownEditor::make('content')
                                    ->required(),
                                Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->options(ArticleCategory::all()->pluck('name', 'id')->take(5))
                                    ->searchable(['name'])
                                    ->required(),
                                DateTimePicker::make('published_at')
                                    ->required()
                                    ->default(now())
                                    ->seconds(false),
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
                TextColumn::make('category.name'),
                TextColumn::make('published_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
