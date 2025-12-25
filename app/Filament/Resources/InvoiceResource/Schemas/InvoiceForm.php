<?php

namespace App\Filament\Resources\InvoiceResource\Schemas;

use App\Enums\AccountType;
use App\Helpers\IdrCurrency;
use App\Models\ExtraCost;
use App\Models\Invoice;
use App\Services\CustomerService\CSService;
use App\Services\CustomerService\CustomerServiceUsageService;
use App\Services\ExtraCostService;
use App\Services\UserService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\Carbon;
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
                                        $set('user_id', []);
                                        $set('customer_services', []);
                                    }),

                                Select::make('user_id')
                                    ->label('Pelanggan')
                                    ->options(function (Get $get): array {
                                        $accountType = $get('account_type');

                                        if (!$accountType) {
                                            return [];
                                        }

                                        return UserService::options(
                                            accountType: $accountType,
                                            onlyHasServices: true
                                        );
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn(Get $get): bool => !$get('account_type'))
                                    ->required()
                                    ->native(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set): void {
                                        $set('customer_services', []);
                                    })
                            ]),

                        Section::make('Item Tagihan')
                            ->schema([
                                Repeater::make('customer_services')
                                    ->label('Item Layanan')
                                    ->hiddenLabel()
                                    ->required()
                                    ->defaultItems(0)
                                    ->debounce()
                                    ->schema([
                                        Select::make('customer_service_id')
                                            ->label('Layanan')
                                            ->options(function (?Invoice $invoice, Get $get): array {
                                                $userId = $invoice?->user_id ?? $get('../../user_id');

                                                if (!$userId) return [];

                                                return collect(CSService::options(userId: $userId))
                                                    ->map(fn($data) => $data['name'])
                                                    ->toArray();
                                            })
                                            ->required()
                                            ->native(false)
                                            ->debounce()
                                            ->reactive(),

                                    ])
                                    ->addActionLabel('Tambah Item'),

                                CheckboxList::make('extra_costs')
                                    ->label('Biaya Tambahan')
                                    ->bulkToggleable()
                                    ->options(function (Get $get): array {
                                        $customerServiceId = $get('customer_service_id');

                                        if (!$customerServiceId) return [];

                                        return collect(ExtraCostService::options($customerServiceId))
                                            ->map(fn($item) => $item['name'])
                                            ->toArray();
                                    })
                                    ->debounce()
                                    ->reactive(),
                            ]),

                        Section::make('Masa Faktur')
                            ->columns()
                            ->schema([
                                DatePicker::make('date')
                                    ->label('Tanggal')
                                    ->native(false)
                                    ->default(now())
                                    ->minDate(function (Get $get): Carbon|null {
                                        $customerServices = $get('customer_services');

                                        if (! $customerServices) return null;

                                        $collection = collect($customerServices);

                                        $customerServiceIds = $collection
                                            ->pluck('customer_service_id')
                                            ->all();

                                        return CustomerServiceUsageService::lastUsagePeriod($customerServiceIds);
                                    })
                                    ->maxDate(now())
                                    ->required()
                                    ->placeholder('Masukkan tanggal faktur')
                                    ->reactive()
                                    ->closeOnDateSelection(),

                                DatePicker::make('due_date')
                                    ->label('Tanggal Jatuh Tempo')
                                    ->native(false)
                                    ->required()
                                    ->minDate(function (Get $get): Carbon|null {
                                        $date = $get('date');

                                        if (!$date) return Carbon::now();

                                        return Carbon::parse($date);
                                    })
                                    ->maxDate(now()->endOfMonth())
                                    ->placeholder('Masukkan tanggal jatuh tempo')
                                    ->closeOnDateSelection(),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Ringkasan')
                            ->collapsible()
                            ->schema(function (?Invoice $invoice, Get $get): array {
                                $userId = $invoice?->user_id ?? $get('user_id');
                                $customerService = $get('customer_services');

                                if (!$userId || !$customerService) return [];

                                $schema = self::itemSummary($userId, $customerService);

                                $items = $schema['schema'];

                                return array_merge($items, [
                                    Placeholder::make('Total Tagihan')
                                        ->content(fn(): string|HtmlString => new HtmlString('<span style="font-weight: bold; color: #00bb00; font-size: large">'. IdrCurrency::convert($schema['totalBill']) .'</span>'))
                                ]);
                            })
                    ]),
            ]);
    }

    private static function itemSummary($userId, $items): array
    {
        $schema = [];
        $totalBill = 0;
        $number = 0;

        $collection = collect($items);

        $customerServiceIds = $collection
            ->pluck('customer_service_id')
            ->all();

        $extraCostIds = $collection
            ->pluck('extra_costs')
            ->flatten()
            ->all();

        $customerServices = CSService::options(userId: $userId, selfIds: $customerServiceIds);
        $extraCosts = ExtraCost::whereIn('id', $extraCostIds)
            ->get();

        $extraCostTotal = $extraCosts->sum('fee');

        $extraCostSchemas = [];
        foreach ($extraCosts as $extraCost) {
            $fee = $extraCost->fee;
            $extraCostSchemas[] = Placeholder::make($extraCost->name)
                ->content(IdrCurrency::convert($fee));
        }

        foreach ($customerServices as $customerService) {
            $number++;

            $price = $customerService['price'];

            $schema[] = Section::make('Item ' . $number)
                ->schema(
                    array_merge([
                        Placeholder::make($customerService['name'])
                            ->content(IdrCurrency::convert($price))
                    ], $extraCostSchemas)
                );

            $totalBill += $price + $extraCostTotal;
        }

        return [
            'schema' => $schema,
            'totalBill' => $totalBill
        ];
    }
}
