<?php

namespace App\Filament\Resources\InvoiceResource\Schemas;

use App\Enums\AccountType;
use App\Helpers\IdrCurrency;
use App\Models\CustomerService;
use App\Models\ExtraCost;
use App\Models\Invoice;
use App\Services\CustomerService\CSService;
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
                                /*CheckboxList::make('invCustomerServices')
                                    ->label('Item Layanan')
                                    ->bulkToggleable()
                                    ->options(function (?Invoice $invoice, Get $get): array {
                                        $userId = $invoice?->user_id ?? $get('user_id');

                                        if (!$userId) return [];

                                        return collect(CSService::options(userId: $userId))
                                            ->map(fn($data) => $data['name'])
                                            ->toArray();
                                    })
                                    ->descriptions(function (?Invoice $invoice, Get $get): array {
                                        $userId = $invoice?->user_id ?? $get('user_id');

                                        if (!$userId) return [];

                                        return collect(CSService::options(userId: $userId))
                                            ->map(fn($data) => 'Rp' . number_format($data['price'], 0, ',', '.') . ' (' . $data['packageType'] . ')')
                                            ->toArray();
                                    })
                                    ->required()
                                    ->reactive(),

                                CheckboxList::make('invExtraCosts')
                                    ->label('Biaya Tambahan')
                                    ->bulkToggleable()
                                    ->columns()
                                    ->options(function (Get $get): array {
                                        //$customerServiceId = $get('')
                                        return collect(ExtraCostService::options(BillingType::RECURRING->value))
                                            ->map(fn($item) => $item['name'])
                                            ->toArray();
                                    })
                                    ->descriptions(function (): array {
                                        return collect(ExtraCostService::options(BillingType::RECURRING->value))
                                            ->map(fn($data) => 'Rp' . number_format($data['fee'], 0, ',', '.'))
                                            ->toArray();
                                    })
                                    ->reactive()*/

                                Repeater::make('customer_services')
                                    ->label('Item Layanan')
                                    ->hiddenLabel()
                                    ->required()
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
                                            ->required()
                                            ->debounce()
                                            ->reactive(),
                                    ])
                                    ->addActionLabel('Tambah Item')
                            ])
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Ringkasan')
                            ->collapsible()
                            /*->schema([
                                Placeholder::make('total')
                                    ->label('Total Faktur')
                                    ->content(function (Get $get): string|HtmlString {
                                        $userId = $get('user_id');

                                        if (!$userId) {
                                            $total = 0;
                                        }else {
                                            // Ambil data item layanan
                                            $totalInvoice = collect(CSService::options($userId))
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
                            ])*/
                            ->schema(function (Get $get): array {
                                $schema = self::itemSummary($get('customer_services'));

                                $items = $schema['schema'];

                                return array_merge($items, [
                                    Placeholder::make('Total Tagihan')
                                        ->content(fn(): string|HtmlString => new HtmlString('<span style="font-weight: bold; color: #00bb00; font-size: large">'. IdrCurrency::convert($schema['totalBill']) .'</span>'))
                                ]);
                            })
                    ]),
            ]);
    }

    private static function itemSummary($items): array
    {
        $schema = [];
        $totalBill = 0;
        $number = 0;

        foreach ($items as $item) {
            $number++;

            $customerService = CustomerService::with('servicePackage')
                ->find($item['customer_service_id']);
            $extraCosts = ExtraCost::whereIn('id', $item['extra_costs'] ?? [])
                ->get();

            $customerServicePrice = (int)$customerService?->price;

            $customerServiceItems = [
                Placeholder::make($customerService?->servicePackage?->package_name ?? 'N/A')
                    ->content(IdrCurrency::convert($customerServicePrice))
            ];

            $extraCostItems = [];
            $extraCostFees = 0;
            foreach ($extraCosts as $extraCost) {
                $fee = $extraCost->fee;
                $extraCostItems[] = Placeholder::make($extraCost->name)
                    ->content(IdrCurrency::convert($fee));

                $extraCostFees += $fee;
            }


            $schema[] = Section::make('Item #' . $number)
                ->schema(array_merge($customerServiceItems, $extraCostItems));

            $totalBill += $customerServicePrice + $extraCostFees;
        }

        return [
            'schema' => $schema,
            'totalBill' => $totalBill
        ];
    }
}
