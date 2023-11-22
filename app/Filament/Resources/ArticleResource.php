<?php

namespace App\Filament\Resources;

use App\Enums\NavigationGroup;
use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Models\ArticleCategory;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

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
                // Media Group
                Group::make()
                    ->schema([
                        Section::make('Media')
                            ->description('Article Media')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('image'),
                                TextInput::make('copyright'),
                                TextInput::make('caption'),
                                SpatieMediaLibraryFileUpload::make('trending_image')
                                    ->helperText('In case the article is trending, choose a different image.'),
                                TextInput::make('trending_copyright'),
                            ])
                    ])
                    ->columnSpanFull(),
                // Basic Group
                Group::make()
                    ->schema([
                        Section::make('Basic')
                            ->description('Basic article information')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        if (($get('slug') ?? '') !== Str::slug($old)) {
                                            return;
                                        }
                                        $set('slug', Str::slug($state));
                                    })
                                    ->helperText('Main title that will be used for the Article. It will also be the SEO title in case none is provided. Choose wisely!'),
                                TextInput::make('slug')->readOnly(),
                                RichEditor::make('content')
                                    ->required(),
                                Select::make('dossier_id')
                                    ->relationship('dossier', 'title')
                                    ->searchable(['title']),
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
                // Author
                Group::make()
                    ->schema([
                        Section::make('Author')
                            ->description('Author of the article')
                            ->schema([
                                Select::make('author_id')
                                    ->label('Author (Backend)')
                                    ->relationship('author', 'name')
                                    ->default(auth()->id())
                                    ->searchable(['name'])
                                    ->required(),
                                Select::make('front_author_id')
                                    ->relationship('author', 'name')
                                    ->default(auth()->id())
                                    ->searchable(['name'])
                                    ->required(),
                            ])
                    ])
                    ->columnSpanFull(),
                // Relationships
                
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
