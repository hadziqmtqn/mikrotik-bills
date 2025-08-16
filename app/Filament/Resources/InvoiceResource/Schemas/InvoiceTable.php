<?php

namespace App\Filament\Resources\InvoiceResource\Schemas;

use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoiceTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name'),

                TextColumn::make('date')
                    ->date(),

                TextColumn::make('due_date')
                    ->date(),

                TextColumn::make('cancel_date')
                    ->date(),

                TextColumn::make('status'),

                TextColumn::make('note'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                //
            ]);
    }
}
