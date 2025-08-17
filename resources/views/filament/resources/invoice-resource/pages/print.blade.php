@php use App\Helpers\DateHelper; @endphp

<link rel="stylesheet" href="{{ asset('css/invoice/style.css') }}">

<div>
    <div class="ci-py-4">
        <div class="ci-px-14 ci-py-6">
            <table class="ci-w-full ci-border-collapse ci-border-spacing-0">
                <tbody>
                <tr>
                    <td class="ci-w-full ci-align-top">
                        <div>
                            <img src="{{ $application?->invoice_logo }}" class="ci-h-12"  alt="Logo"/>
                        </div>
                    </td>
                    <td class="ci-align-top">
                        <div class="ci-text-sm">
                            <table class="ci-border-collapse ci-border-spacing-0">
                                <tbody>
                                <tr>
                                    <td class="ci-border-r ci-pr-4">
                                        <div>
                                            <p class="ci-whitespace-nowrap ci-text-slate-400 ci-text-right">Tanggal</p>
                                            <p class="ci-whitespace-nowrap ci-font-bold ci-text-main ci-text-right">{{ DateHelper::indonesiaDate($invoice->date, 'D MMM Y') }}</p>
                                        </div>
                                    </td>
                                    <td class="ci-pl-4">
                                        <div>
                                            <p class="ci-whitespace-nowrap ci-text-slate-400 ci-text-right">Invoice #</p>
                                            <p class="ci-whitespace-nowrap ci-font-bold ci-text-main ci-text-right">{{ $invoice->code }}</p>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="ci-bg-slate-100 ci-px-14 ci-py-6 ci-text-sm">
            <table class="ci-w-full ci-border-collapse ci-border-spacing-0">
                <tbody>
                <tr>
                    <td class="ci-w-1/2 ci-align-top">
                        <div class="ci-text-sm ci-text-neutral-600">
                            <p class="ci-font-bold">Dari</p>
                            <p>{{ $application?->business_name }}</p>
                            <p>{{ $application?->business_email }}</p>
                            <p>{{ $application?->business_phone }}</p>
                            <p>{{ $application?->business_address }}</p>
                        </div>
                    </td>
                    <td class="ci-w-1/2 ci-align-top ci-text-right">
                        <div class="ci-text-sm ci-text-neutral-600">
                            <p class="ci-font-bold">Untuk</p>
                            <p>{{ $invoice->user?->name }}</p>
                            <p>{{ $invoice->user?->userProfile?->whatsapp_number }}</p>
                            <p>{{ $invoice->user?->userProfile?->street }}</p>
                            <p>{{ $invoice->user?->userProfile?->village }}, {{ $invoice->user?->userProfile?->district }}</p>
                            <p>{{ $invoice->user?->userProfile?->city }}, {{ $invoice->user?->userProfile?->postal_code }}</p>
                            <p>{{ $invoice->user?->userProfile?->province }}</p>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="ci-px-14 ci-py-10 ci-text-sm ci-text-neutral-700">
            <table class="ci-w-full ci-border-collapse ci-border-spacing-0">
                <thead>
                <tr>
                    <td class="ci-border-b-2 ci-border-main ci-pb-3 ci-pl-3 ci-font-bold ci-text-main">#</td>
                    <td class="ci-border-b-2 ci-border-main ci-pb-3 ci-pl-2 ci-font-bold ci-text-main">Nama Item</td>
                    <td class="ci-border-b-2 ci-border-main ci-pb-3 ci-pl-2 ci-text-right ci-font-bold ci-text-main">Harga</td>
                    <td class="ci-border-b-2 ci-border-main ci-pb-3 ci-pl-2 ci-text-center ci-font-bold ci-text-main">Qty.</td>
                    <td class="ci-border-b-2 ci-border-main ci-pb-3 ci-pl-2 ci-text-right ci-font-bold ci-text-main">Subtotal</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    @foreach($invoice->invoiceItems as $item)
                        <td class="ci-border-b ci-py-3 ci-pl-3">{{ $loop->iteration }}.</td>
                        <td class="ci-border-b ci-py-3 ci-pl-2">{{ $item->customerService?->servicePackage?->package_name }}</td>
                        <td class="ci-border-b ci-py-3 ci-pl-2 ci-text-right">Rp{{ number_format($item->customerService?->price,0,',','.') }}</td>
                        <td class="ci-border-b ci-py-3 ci-pl-2 ci-text-center">1</td>
                        <td class="ci-border-b ci-py-3 ci-pl-2 ci-text-right">Rp{{ number_format($item->customerService?->price,0,',','.') }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td colspan="5">
                        <table class="ci-w-full ci-border-collapse ci-border-spacing-0">
                            <tbody>
                            <tr>
                                <td class="ci-w-full"></td>
                                <td>
                                    <table class="ci-w-full ci-border-collapse ci-border-spacing-0">
                                        <tbody>
                                        <tr>
                                            <td class="ci-border-b ci-p-3">
                                                <div class="ci-whitespace-nowrap ci-text-slate-400">Sub Total:</div>
                                            </td>
                                            <td class="ci-border-b ci-p-3 ci-text-right">
                                                <div class="ci-whitespace-nowrap ci-font-bold ci-text-main">Rp{{ number_format($invoice->total_price,0,',','.') }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ci-bg-main ci-p-3">
                                                <div class="ci-whitespace-nowrap ci-font-bold ci-text-white">Total:</div>
                                            </td>
                                            <td class="ci-bg-main ci-p-3 ci-text-right">
                                                <div class="ci-whitespace-nowrap ci-font-bold ci-text-white">Rp{{ number_format($invoice->total_price,0,',','.') }}</div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="ci-px-14 ci-text-sm ci-text-neutral-700">
            <p class="ci-text-main ci-font-bold">DETAIL PEMBAYARAN</p>
            <ul>
                @foreach($bankAccounts as $bankAccount)
                    <li>{{ $bankAccount->short_name }} - <strong>{{ $bankAccount->account_number }}</strong> a/n {{ $bankAccount->account_name }}</li>
                @endforeach
            </ul>
        </div>

        <div class="ci-px-14 ci-py-10 ci-text-sm ci-text-neutral-700">
            <p class="ci-text-main ci-font-bold">Notes</p>
            <p class="ci-italic">Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.</p>
        </div>
    </div>
</div>