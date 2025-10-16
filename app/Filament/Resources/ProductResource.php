<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Prodotti';
    protected static ?string $pluralModelLabel = 'Prodotti';
    protected static ?string $modelLabel = 'Prodotto';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nome')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->label('Slug')->required()->maxLength(255),
            Forms\Components\Textarea::make('description')->label('Descrizione')->rows(3),

            Forms\Components\TextInput::make('price_cents')
                ->label('Prezzo (cent)')
                ->numeric()->required()->minValue(0),

            Forms\Components\TextInput::make('sku')
                ->label('SKU')->required()->maxLength(50),

            Forms\Components\TextInput::make('stock')
                ->label('Stock')->numeric()->required()->minValue(0),

            Forms\Components\Toggle::make('is_visible')->label('Visibile')->default(true),

            Forms\Components\Select::make('categories')
                ->label('Categorie')
                ->relationship('categories', 'name')
                ->multiple()
                ->preload()
                ->searchable(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('sku')->label('SKU')->sortable(),
                Tables\Columns\TextColumn::make('price_cents')->label('Prezzo (cent)')->sortable(),
                Tables\Columns\TextColumn::make('stock')->label('Stock')->sortable(),
                Tables\Columns\IconColumn::make('is_visible')->label('Visibile')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
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
