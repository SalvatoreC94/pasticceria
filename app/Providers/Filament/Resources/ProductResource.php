<?php

namespace App\Filament\Resources;

use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Catalogo';
    protected static ?string $navigationLabel = 'Prodotti';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('category_id')
                ->label('Categoria')
                ->relationship('category', 'name')
                ->required()
                ->searchable(),
            TextInput::make('name')
                ->label('Nome')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true),
            Textarea::make('description')
                ->label('Descrizione')
                ->rows(4),
            TextInput::make('price_cents')
                ->label('Prezzo (centesimi)')
                ->numeric()
                ->required(),
            TextInput::make('weight_grams')
                ->label('Peso (g)')
                ->numeric()
                ->default(0),
            TagsInput::make('allergens')
                ->label('Allergeni')
                ->suggestions(['glutine', 'latte', 'uova', 'frutta a guscio', 'soia']),
            Toggle::make('is_visible')
                ->label('Visibile')
                ->default(true),
            Toggle::make('is_made_to_order')
                ->label('Su ordinazione'),
            TextInput::make('lead_time_hours')
                ->label('Lead time (ore)')
                ->numeric()
                ->default(0),
            TextInput::make('stock')
                ->label('Stock')
                ->numeric()
                ->nullable(),
            FileUpload::make('image_path')
                ->label('Immagine')
                ->directory('products')
                ->image()
                ->imageEditor(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('image_path')
                ->label('Immagine')
                ->square(),
            TextColumn::make('name')
                ->label('Nome')
                ->searchable()
                ->sortable(),
            TextColumn::make('category.name')
                ->label('Categoria')
                ->sortable(),
            TextColumn::make('price_cents')
                ->label('Prezzo')
                ->formatStateUsing(fn ($v) => number_format($v / 100, 2, ',', '.') . ' €')
                ->sortable(),
            IconColumn::make('is_visible')
                ->label('Visibile')
                ->boolean(),
            TextColumn::make('stock')
                ->label('Stock')
                ->sortable(),
            TextColumn::make('created_at')
                ->label('Creato')
                ->since(),
        ])
        ->filters([
            SelectFilter::make('category_id')
                ->label('Categoria')
                ->relationship('category', 'name'),
        ])
        ->actions([
            Tables\Actions\EditAction::make()->label('Modifica'),
            Tables\Actions\DeleteAction::make()->label('Elimina'),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->label('Elimina selezionati'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Filament\Resources\Pages\ListRecords::class,
            'create' => \Filament\Resources\Pages\CreateRecord::class,
            'edit'   => \Filament\Resources\Pages\EditRecord::class,
        ];
    }
}
