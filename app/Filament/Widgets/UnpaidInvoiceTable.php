<?php

namespace App\Filament\Widgets;

use App\Enums\StatusData;
use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class UnpaidInvoiceTable extends TableWidget
{
    protected int | string | array $columnSpan = ['lg' => 2];

    protected static ?string $heading = 'Faktur belum dibayar';

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                return Invoice::query()
                    ->with('user.userProfile')
                    ->whereIn('status', [StatusData::PENDING->value, StatusData::UNPAID->value]);
            })
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->searchable(),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR'),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->formatStateUsing(fn($state): string => Carbon::parse($state)->isoFormat('D MMM Y'))
                    ->description(fn(Invoice $invoice): string => 'Jatuh Tempo: ' . Carbon::parse($invoice->due_date)->isoFormat('D MMM Y')),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state): string => StatusData::tryFrom($state)?->getColor() ?? 'gray')
                    ->formatStateUsing(fn($state): string => StatusData::tryFrom($state)?->getLabel() ?? $state)
            ])
            ->actions([
                Action::make('view')
                    ->label('Detail')
                    ->button()
                    ->url(fn(Invoice $invoice): string => InvoiceResource::getUrl('view', ['record' => $invoice]))
            ])
            ->defaultSort('date', 'DESC')
            ->deferLoading()
            ->defaultPaginationPageOption(5)
            ->filters([
                Filter::make('date')
                    ->form([
                        DatePicker::make('first_date')
                            ->label('Tanggal Mulai')
                            ->native(false)
                            ->maxDate(now())
                            ->placeholder('Tanggal Mulai')
                            ->closeOnDateSelection(),

                        DatePicker::make('last_date')
                            ->label('Sampai')
                            ->native(false)
                            ->maxDate(now())
                            ->placeholder('Sampai')
                            ->closeOnDateSelection(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $firstDate = $data['first_date'] ?? null;
                        $lastDate = $data['last_date'] ?? null;

                        return $query->when($firstDate && $lastDate, function (Builder $query) use ($firstDate, $lastDate) {
                            $query->whereBetween('date', [$firstDate, $lastDate]);
                        });
                    })
            ]);
    }
}
