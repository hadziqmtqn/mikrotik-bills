<?php

namespace App\Filament\Resources\InvoiceResource\Schemas;

use App\Enums\AccountType;
use App\Enums\BillingType;
use App\Models\Invoice;
use App\Services\CustomerServicesService;
use App\Services\ExtraCostService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class InvoiceForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Data Pelanggan')
                            ->schema([
                                ToggleButtons::make('account_type')
                                    ->label('Jenis Pelanggan')
                                    ->options(AccountType::options())
                                    ->inline()
                                    ->required()
                                    ->dehydrated(false)
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set): void {
                                        $set('user_id', null);
                                    }),

                                Select::make('user_id')
                                    ->label('Pelanggan')
                                    ->relationship(name: 'user', titleAttribute: 'name', modifyQueryUsing: function (Builder $query, callable $get) {
                                        $accountType = $get('account_type');

                                        $query->whereHas('roles', fn(Builder $query) => $query->where('name', 'user'));
                                        $query->whereHas('userProfile', function (Builder $query) use ($accountType) {
                                            $query->when($accountType, fn(Builder $query) => $query->where('account_type', $accountType));
                                        });
                                        $query->where('is_active', true);
                                        $query->orderBy('name');
                                    })
                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => $record->name . ' (' . $record->userProfile?->ppoe_name . ')')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn(Get $get): bool => !$get('account_type'))
                                    ->required()
                                    ->native(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set): void {
                                        $set('inv_customer_services', []);
                                    })
                            ]),

                        Section::make('Masa Faktur')
                            ->columns()
                            ->schema([
                                DatePicker::make('date')
                                    ->label('Tanggal')
                                    ->native(false)
                                    ->default(now())
                                    ->required()
                                    ->placeholder('Masukkan tanggal faktur')
                                    ->closeOnDateSelection(),

                                DatePicker::make('due_date')
                                    ->label('Tanggal Jatuh Tempo')
                                    ->native(false)
                                    ->required()
                                    ->default(now()->setDay(20))
                                    ->maxDate(now()->endOfMonth())
                                    ->placeholder('Masukkan tanggal jatuh tempo')
                                    ->closeOnDateSelection(),
                            ]),

                        Section::make('Item Tagihan')
                            ->schema([
                                CheckboxList::make('invCustomerServices')
                                    ->label('Item Layanan')
                                    ->bulkToggleable()
                                    ->options(function (?Invoice $invoice, Get $get): array {
                                        $userId = $invoice?->user_id ?? $get('user_id');

                                        if (!$userId) return [];

                                        return collect(CustomerServicesService::options(userId: $userId))
                                            ->map(fn($data) => $data['name'])
                                            ->toArray();
                                    })
                                    ->descriptions(function (?Invoice $invoice, Get $get): array {
                                        $userId = $invoice?->user_id ?? $get('user_id');

                                        if (!$userId) return [];

                                        return collect(CustomerServicesService::options(userId: $userId))
                                            ->map(fn($data) => 'Rp' . number_format($data['price'], 0, ',', '.') . ' (' . $data['packageType'] . ')')
                                            ->toArray();
                                    })
                                    ->required()
                                    ->reactive(),

                                CheckboxList::make('invExtraCosts')
                                    ->label('Biaya Tambahan')
                                    ->bulkToggleable()
                                    ->columns()
                                    ->options(function (): array {
                                        return collect(ExtraCostService::options(BillingType::RECURRING->value))
                                            ->map(fn($item) => $item['name'])
                                            ->toArray();
                                    })
                                    ->descriptions(function (): array {
                                        return collect(ExtraCostService::options(BillingType::RECURRING->value))
                                            ->map(fn($data) => 'Rp' . number_format($data['fee'], 0, ',', '.'))
                                            ->toArray();
                                    })
                                    ->reactive()
                            ])
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Ringkasan')
                            ->schema([
                                Placeholder::make('total')
                                    ->label('Total Faktur')
                                    ->content(function (Get $get): string|HtmlString {
                                        $userId = $get('user_id');

                                        if (!$userId) {
                                            $total = 0;
                                        }else {
                                            // Ambil data item layanan
                                            $totalInvoice = collect(CustomerServicesService::options($userId))
                                                ->only(collect($get('invCustomerServices') ?? []))
                                                ->sum('price');

                                            // Ambil data biaya tambahan
                                            $totalExtra = collect(ExtraCostService::options(BillingType::RECURRING->value))
                                                ->only(collect($get('invExtraCosts') ?? []))
                                                ->sum('fee');

                                            $total = $totalInvoice + $totalExtra;
                                            $total = number_format($total, 0, ',', '.');
                                        }

                                        return new HtmlString('<span style="font-weight: bold; color: #00bb00; font-size: large">Rp '. $total .'</span>');
                                    })
                                    ->reactive(),
                            ])
                    ]),
            ]);
    }
}
