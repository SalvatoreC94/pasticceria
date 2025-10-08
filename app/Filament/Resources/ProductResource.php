<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Catalogo';
    protected static ?string $navigationLabel = 'Prodotti';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nome')
                ->required()
                ->live()
                ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Str::slug($state))),
            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('sku')
                ->label('SKU')
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('description')->label('Descrizione'),
            Forms\Components\TextInput::make('price_cents')->label('Prezzo (centesimi)')->numeric()->minValue(0)->required(),
            Forms\Components\TextInput::make('stock_qty')->label('Q.tà')->numeric()->minValue(0)->required(),
            Forms\Components\Toggle::make('is_visible')->label('Visibile')->default(true),
            Forms\Components\FileUpload::make('images')->label('Immagini')->image()->multiple(),
            Forms\Components\Select::make('categories')->label('Categorie')->relationship('categories', 'name')->multiple()->preload(),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('sku')->toggleable(),
                Tables\Columns\TextColumn::make('price_cents')->money('EUR', divideBy: 100)->label('Prezzo')->sortable(),
                Tables\Columns\TextColumn::make('stock_qty')->label('Q.tà')->sortable(),
                Tables\Columns\IconColumn::make('is_visible')->boolean()->label('Vis.'),
                Tables\Columns\TextColumn::make('categories.name')->label('Categorie')->limit(30),
                Tables\Columns\TextColumn::make('updated_at')->dateTime('d/m/Y H:i')->label('Agg.'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
