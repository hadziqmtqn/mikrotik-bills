<?php

namespace App\Filament\Resources\CustomerServiceResource\Pages;

use App\Filament\Resources\CustomerServiceResource\CustomerServiceResource;
use App\Filament\Resources\InvoiceResource\InvoiceResource;
use App\Helpers\DateHelper;
use App\Models\CustomerServiceUsage;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ManageCustomerServiceUsage extends ManageRelatedRecords
{
    protected static string $resource = CustomerServiceResource::class;

    protected static string $relationship = 'customerServiceUsages';

    protected static ?string $breadcrumb = 'Daftar Penggunaan';

    protected static ?string $title = 'Riwayat Penggunaan';

    protected static ?string $navigationIcon = 'heroicon-o-folder-minus';

    public static function getNavigationLabel(): string
    {
        return 'Riwayat Penggunaan';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('invoice.code')
                    ->label('Kode Fakturr')
                    ->searchable()
                    ->color('primary')
                    ->url(fn(CustomerServiceUsage $customerServiceUsage): string => InvoiceResource::getUrl('view', ['record' => $customerServiceUsage->invoice])),

                Tables\Columns\TextColumn::make('period_start')
                    ->label('Periode')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function (CustomerServiceUsage $customerServiceUsage): string {
                        return DateHelper::indonesiaDate($customerServiceUsage->period_start, 'D MMM Y') . ' - ' . DateHelper::indonesiaDate($customerServiceUsage->period_end, 'D MMM Y');
                    })
                    ->description(fn(CustomerServiceUsage $customerServiceUsage): string => 'Penggunaan: ' . $customerServiceUsage->days_of_usage . ' hari'),

                Tables\Columns\TextColumn::make('daily_price')
                    ->label('Tagihan Harian')
                    ->sortable()
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total Tagihan')
                    ->sortable()
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('next_billing_date')
                    ->label('Tagihan Berikutnya')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state): string => DateHelper::indonesiaDate($state, 'D MMM Y')),
            ])
            ->deferLoading()
            ->defaultSort('period_start', 'DESC')
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
