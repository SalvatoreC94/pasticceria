<?php

namespace App\Filament\Resources;

use App\Models\Category;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Catalogo';
    protected static ?string $navigationLabel = 'Categorie';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nome')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true),
            Toggle::make('is_visible')
                ->label('Visibile')
                ->default(true),
            Textarea::make('description')
                ->label('Descrizione')
                ->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->label('Nome')
                ->searchable()
                ->sortable(),
            TextColumn::make('slug')
                ->label('Slug')
                ->toggleable(),
            IconColumn::make('is_visible')
                ->label('Visibile')
                ->boolean(),
            TextColumn::make('products_count')
                ->counts('products')
                ->label('# Prodotti'),
            TextColumn::make('created_at')
                ->label('Creato')
                ->since(),
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
