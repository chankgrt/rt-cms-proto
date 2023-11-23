<?php

namespace App\Filament\Resources;

use App\Enums\NavigationGroup;
use App\Filament\Blocks\Article\ContentBlock;
use App\Filament\Blocks\Article\SpecificArticle;
use App\Filament\Fields\UserSelect;
use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Builder;
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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
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
                // Article Body
                Group::make()
                    ->schema([
                        Section::make('Article Body')
                            ->description('This will be the main article copyright, articles copy are based on Blocks, just like Wordpress, click on "Add new block!" to add a new block. You can also re-order the blocks by clicking the little arrow icon next to the delete button.')
                            ->schema([
                                Builder::make('content')
                                    ->blocks([
                                        ContentBlock::make(),
                                        SpecificArticle::make(),
                                    ]),
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
                Group::make()
                    ->schema([
                        Section::make('Relationships')
                            ->schema([
                                Forms\Components\Tabs::make('Relationships')
                                    ->tabs([
                                        Forms\Components\Tabs\Tab::make('Tags')
                                            ->schema([
                                                Select::make('tag_id')
                                                    ->label('Tag Title')
                                                    ->multiple()
                                                    ->relationship('tags', 'title') // Assuming 'name' is the attribute you want to display from the Tag model
                                                    ->required(),
                                            ]),
                                        Forms\Components\Tabs\Tab::make('Persons'),
                                        Forms\Components\Tabs\Tab::make('Matches'),
                                        Forms\Components\Tabs\Tab::make('Tournaments'),
                                        Forms\Components\Tabs\Tab::make('Teams'),
                                    ])
                            ])
                    ])
                    ->columnSpanFull(),
                // Toggles & Status
                Group::make()
                    ->schema([
                        Section::make('Toggles & Status')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->helperText('Article wont be visible unless you check this box. This is useful for articles that are not yet ready to be published.'),
                                Forms\Components\Toggle::make('show_read_time')
                                    ->label('Show Read Time'),
                                Forms\Components\Toggle::make('is_important')
                                    ->label('Important'),
                            ])
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('author.name')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->disabledClick(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ReplicateAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->action(function (Collection $records) {
                            // Get the IDs of all selected records
                            $ids = $records->pluck('id');

                            // Perform a bulk update
                            Article::query()->whereIn('id', $ids)->update(['is_active' => 1]);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->label('Activate')
                        ->color('success'),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->action(function (Collection $records) {
                            // Get the IDs of all selected records
                            $ids = $records->pluck('id');

                            // Perform a bulk update
                            Article::query()->whereIn('id', $ids)->update(['is_active' => 0]);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->label('Deactivate')
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
            'view' => Pages\ViewArticle::route('/{record}'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
