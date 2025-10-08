<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Articoli';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Prodotto'),
                Tables\Columns\TextColumn::make('price_cents')->label('Prezzo')->money('EUR', divideBy: 100),
                Tables\Columns\TextColumn::make('qty')->label('Q.tà'),
                Tables\Columns\TextColumn::make('line')->label('Riga')->state(fn ($r) => $r->price_cents * $r->qty)->money('EUR', divideBy: 100),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
