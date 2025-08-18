<?php

namespace App\Filament\Resources\CustomerServiceResource\Pages;

use App\Enums\StatusData;
use App\Filament\Resources\CustomerServiceResource;
use App\Filament\Resources\InvoiceResource;
use App\Helpers\DateHelper;
use App\Models\Application;
use App\Models\BankAccount;
use App\Models\InvoiceItem;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\View\View;
use Torgodly\Html2Media\Tables\Actions\Html2MediaAction;

class InvoiceHistory extends ManageRelatedRecords
{
    protected static string $resource = CustomerServiceResource::class;
    protected static string $relationship = 'invoiceItems';
    protected static ?string $title = 'Invoice History';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice.code')
                    ->label('Kode')
                    ->searchable(),

                TextColumn::make('invoice.user.name')
                    ->label('Pelanggan')
                    ->searchable(),

                TextColumn::make('invoice.date')
                    ->label('Tanggal')
                    ->date()
                    ->formatStateUsing(fn ($state): string => DateHelper::indonesiaDate($state, 'D MMM Y HH:mm')),

                TextColumn::make('invoice.due_date')
                    ->label('Jatuh Tempo')
                    ->date()
                    ->formatStateUsing(fn ($state): string => DateHelper::indonesiaDate($state, 'D MMM Y HH:mm')),

                TextColumn::make('invoice.total_price')
                    ->label('Total Harga')
                    ->money('idr'),

                TextColumn::make('invoice.status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state): string => StatusData::tryFrom($state)->getLabel())
                    ->color(fn($state): string => StatusData::tryFrom($state)->getColor()),

                TextColumn::make('invoice.cancel_date')
                    ->label('Tanggal Dibatalkan')
                    ->date()
                    ->formatStateUsing(fn ($state): string => DateHelper::indonesiaDate($state, 'D MMM Y HH:mm'))
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Html2MediaAction::make('export')
                        ->label('Cetak')
                        ->icon('heroicon-o-printer')
                        ->color('primary')
                        ->modalHeading('Cetak Invoice')
                        ->modalDescription('Apakah Anda yakin ingin mencetak invoice ini?')
                        ->successNotificationTitle('Invoice berhasil dicetak.')
                        ->savePdf()
                        ->content(function (InvoiceItem $record): View {
                            $record->loadMissing('invoice.user:id,name,email', 'invoice.user.userProfile', 'invoice.invoiceItems.customerService.servicePackage');

                            return view('filament.resources.invoice-resource.pages.print', [
                                'invoice' => $record->invoice,
                                'application' => Application::first(),
                                'bankAccounts' => BankAccount::where('is_active', true)
                                    ->orderBy('bank_name')
                                    ->get(),
                            ]);
                        })
                        ->filename(fn(InvoiceItem $record): string => 'invoice-' . $record->invoice?->code . '-' . DateHelper::indonesiaDate($record->invoice?->date) . '.pdf'),

                    ViewAction::make()
                        ->url(fn(InvoiceItem $record): string => InvoiceResource::getUrl('view', ['record' => $record->invoice?->slug])),
                ])
                ->link()
                ->label('Action'),
            ]);
    }
}
