<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Essential product details')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                    ->maxLength(255)
                                    ->placeholder('Enter product name'),

                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated()
                                    ->placeholder('auto-generated'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->prefix('SKU-')
                                    ->maxLength(50)
                                    ->placeholder('Product SKU'),

                                TextInput::make('brand')
                                    ->maxLength(100)
                                    ->datalist(['Apple', 'Samsung', 'Google', 'Microsoft', 'Dell']),

                                TextInput::make('category')
                                    ->maxLength(100)
                                    ->datalist(['Electronics', 'Clothing', 'Books', 'Toys', 'Home & Garden']),
                            ]),

                        RichEditor::make('description')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                            ])
                            ->placeholder('Enter detailed product description'),
                    ]),

                Section::make('Pricing & Inventory')
                    ->description('Set prices and manage stock')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(3)
                    ->schema([
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->step(0.01)
                            ->placeholder('0.00'),

                        TextInput::make('cost')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->step(0.01)
                            ->placeholder('0.00')
                            ->helperText('Cost per unit'),

                        TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffix('units')
                            ->live()
                            ->afterStateUpdated(fn ($state, $set) =>
                                $set('is_visible', $state > 0)
                            ),
                    ]),

                Section::make('Media & Assets')
                    ->description('Upload product images')
                    ->icon('heroicon-o-photo')
                    ->collapsed()
                    ->schema([
                        FileUpload::make('images')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(5)
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->directory('products')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),

                Section::make('Organization')
                    ->description('Categorize and tag products')
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->required()
                            ->options([
                                'draft' => 'Draft',
                                'active' => 'Active',
                                'archived' => 'Archived',
                            ])
                            ->default('draft')
                            ->native(false)
                            ->searchable(),

                        DatePicker::make('published_at')
                            ->label('Publish Date')
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->closeOnDateSelection(),

                        TagsInput::make('tags')
                            ->placeholder('Add tags...')
                            ->suggestions(['Featured', 'New Arrival', 'Best Seller', 'Sale', 'Limited Edition'])
                            ->columnSpanFull(),
                    ]),

                Section::make('Settings')
                    ->description('Additional product settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_featured')
                            ->label('Featured Product')
                            ->helperText('Show on homepage')
                            ->inline(false),

                        Toggle::make('is_visible')
                            ->label('Visible to Customers')
                            ->helperText('Hide/show in store')
                            ->default(true)
                            ->inline(false),

                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Created By')
                            ->searchable()
                            ->preload()
                            ->native(false),
                    ]),

                Section::make('Specifications')
                    ->description('Technical details and attributes')
                    ->icon('heroicon-o-list-bullet')
                    ->collapsed()
                    ->schema([
                        KeyValue::make('specifications')
                            ->keyLabel('Attribute')
                            ->valueLabel('Value')
                            ->reorderable()
                            ->addActionLabel('Add specification')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
