<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Enums\StatusData;
use App\Services\ExtraCostService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class InvExtraCostsRelationManager extends RelationManager
{
    protected static string $relationship = 'invExtraCosts';

    protected static ?string $title = 'Biaya Tambahan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Select::make('extra_cost_id')
                            ->label('Biaya Tambahan')
                            ->options(collect(ExtraCostService::options())->map(fn($item) => $item['name'])->toArray())
                            ->native(false)
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('extraCost.name')
                    ->label('Nama'),

                Tables\Columns\TextColumn::make('fee')
                    ->label('Biaya')
                    ->money('IDR')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Baru')
                    ->modalHeading('Tambah Biaya Tambahan')
                    ->modalWidth('md')
                    ->visible(fn(): bool => $this->invoiceStatus()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Ubah Biaya Tambahan')
                    ->modalWidth('md')
                    ->button()
                    ->outlined()
                    ->visible(fn(): bool => $this->invoiceStatus()),

                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Hapus Biaya Tambahan')
                    ->button()
                    ->outlined()
                    ->visible(fn(): bool => $this->invoiceStatus())
            ])
            ->bulkActions([
                //
            ]);
    }

    private function invoiceStatus(): bool
    {
        return $this->ownerRecord->status === StatusData::UNPAID->value;
    }
}
