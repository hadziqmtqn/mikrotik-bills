<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\StatusData;
use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\UserResource;
use App\Helpers\DateHelper;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManageInvoices extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'invoices';

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $title = 'Faktur';

    protected static ?string $navigationLabel = 'Faktur';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->formatStateUsing(fn($state): string => DateHelper::indonesiaDate($state)),

                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->formatStateUsing(fn($state): string => DateHelper::indonesiaDate($state)),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state): string => StatusData::tryFrom($state)?->getColor() ?? 'gray')
                    ->formatStateUsing(fn($state): string => StatusData::tryFrom($state)?->getLabel() ?? $state)
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn(Invoice $invoice): string => InvoiceResource::getUrl('view', ['record' => $invoice]))
                    ->button()
                    ->outlined()
            ]);
    }
}
