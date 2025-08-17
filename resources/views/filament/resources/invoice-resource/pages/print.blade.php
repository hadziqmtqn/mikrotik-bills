<style>
    /* Font styling tetap dipertahankan dan tidak dihapus */
    html {
        line-height: 1.5;
        -webkit-text-size-adjust: 100%;
        -moz-tab-size: 4;
        tab-size: 4;
        font-feature-settings: normal;
        font-variation-settings: normal;
    }
    h1, h2, h3, h4, h5, h6 {
        font-size: inherit;
        font-weight: inherit;
    }
    b, strong {
        font-weight: bolder;
    }
    code, kbd, samp, pre {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 1em;
    }
    small { font-size: 80%; }
    sub, sup {
        font-size: 75%;
        line-height: 0;
        position: relative;
        vertical-align: baseline;
    }
    sub { bottom: -0.25em; }
    sup { top: -0.5em; }
    button, input, optgroup, select, textarea {
        font-family: inherit;
        font-feature-settings: inherit;
        font-variation-settings: inherit;
        font-size: 100%;
        font-weight: inherit;
        line-height: inherit;
        color: inherit;
        margin: 0;
        padding: 0;
    }

    /* Custom Invoice Prefix: ci- */
    .ci-h-12{height:3rem;}
    .ci-w-1\/2{width:50%;}
    .ci-w-full{width:100%;}
    .ci-border-collapse{border-collapse:collapse;}
    .ci-border-spacing-0{border-spacing:0;}
    .ci-whitespace-nowrap{white-space:nowrap;}
    .ci-border-b{border-bottom-width:1px;}
    .ci-border-b-2{border-bottom-width:2px;}
    .ci-border-r{border-right-width:1px;}
    .ci-border-main{border-color:#5c6ac4;}
    .ci-bg-main{background-color:#5c6ac4;}
    .ci-bg-slate-100{background-color:#f1f5f9;}
    .ci-p-3{padding:0.75rem;}
    .ci-px-14{padding-left:3.5rem; padding-right:3.5rem;}
    .ci-py-10{padding-top:2.5rem;padding-bottom:2.5rem;}
    .ci-py-3{padding-top:0.75rem;padding-bottom:0.75rem;}
    .ci-py-4{padding-top:1rem;padding-bottom:1rem;}
    .ci-py-6{padding-top:1.5rem;padding-bottom:1.5rem;}
    .ci-pb-3{padding-bottom:0.75rem;}
    .ci-pl-2{padding-left:0.5rem;}
    .ci-pl-3{padding-left:0.75rem;}
    .ci-pl-4{padding-left:1rem;}
    .ci-pr-3{padding-right:0.75rem;}
    .ci-pr-4{padding-right:1rem;}
    .ci-text-center{text-align:center;}
    .ci-text-right{text-align:right;}
    .ci-align-top{vertical-align:top;}
    .ci-text-sm{font-size:0.875rem;line-height:1.25rem;}
    .ci-font-bold{font-weight:700;}
    .ci-italic{font-style:italic;}
    .ci-text-main{color:#5c6ac4;}
    .ci-text-neutral-600{color:#525252;}
    .ci-text-neutral-700{color:#404040;}
    .ci-text-slate-400{color:#94a3b8;}
    .ci-text-white{color:#fff;}
    @page{margin:0;}
    @media print{
        body{-webkit-print-color-adjust:exact;}
    }
</style>

<div>
    <div class="ci-py-4">
        <div class="ci-px-14 ci-py-6">
            <table class="ci-w-full ci-border-collapse ci-border-spacing-0">
                <tbody>
                <tr>
                    <td class="ci-w-full ci-align-top">
                        <div>
                            <img src="https://raw.githubusercontent.com/templid/email-templates/main/templid-dynamic-templates/invoice-02/brand-sample.png" class="ci-h-12"  alt="Logo"/>
                        </div>
                    </td>
                    <td class="ci-align-top">
                        <div class="ci-text-sm">
                            <table class="ci-border-collapse ci-border-spacing-0">
                                <tbody>
                                <tr>
                                    <td class="ci-border-r ci-pr-4">
                                        <div>
                                            <p class="ci-whitespace-nowrap ci-text-slate-400 ci-text-right">Date</p>
                                            <p class="ci-whitespace-nowrap ci-font-bold ci-text-main ci-text-right">April 26, 2023</p>
                                        </div>
                                    </td>
                                    <td class="ci-pl-4">
                                        <div>
                                            <p class="ci-whitespace-nowrap ci-text-slate-400 ci-text-right">Invoice #</p>
                                            <p class="ci-whitespace-nowrap ci-font-bold ci-text-main ci-text-right">BRA-00335</p>
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
                            <p class="ci-font-bold">Supplier Company INC</p>
                            <p>Number: 23456789</p>
                            <p>VAT: 23456789</p>
                            <p>6622 Abshire Mills</p>
                            <p>Port Orlofurt, 05820</p>
                            <p>United States</p>
                        </div>
                    </td>
                    <td class="ci-w-1/2 ci-align-top ci-text-right">
                        <div class="ci-text-sm ci-text-neutral-600">
                            <p class="ci-font-bold">Customer Company</p>
                            <p>Number: 123456789</p>
                            <p>VAT: 23456789</p>
                            <p>9552 Vandervort Spurs</p>
                            <p>Paradise, 43325</p>
                            <p>United States</p>
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
                    <td class="ci-border-b-2 ci-border-main ci-pb-3 ci-pl-2 ci-font-bold ci-text-main">Product details</td>
                    <td class="ci-border-b-2 ci-border-main ci-pb-3 ci-pl-2 ci-text-right ci-font-bold ci-text-main">Price</td>
                    <td class="ci-border-b-2 ci-border-main ci-pb-3 ci-pl-2 ci-text-center ci-font-bold ci-text-main">Qty.</td>
                    <td class="ci-border-b-2 ci-border-main ci-pb-3 ci-pl-2 ci-text-center ci-font-bold ci-text-main">VAT</td>
                    <td class="ci-border-b-2 ci-border-main ci-pb-3 ci-pl-2 ci-text-right ci-font-bold ci-text-main">Subtotal</td>
                    <td class="ci-border-b-2 ci-border-main ci-pb-3 ci-pl-2 ci-pr-3 ci-text-right ci-font-bold ci-text-main">Subtotal + VAT</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="ci-border-b ci-py-3 ci-pl-3">1.</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2">Montly accounting services</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2 ci-text-right">$150.00</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2 ci-text-center">1</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2 ci-text-center">20%</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2 ci-text-right">$150.00</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2 ci-pr-3 ci-text-right">$180.00</td>
                </tr>
                <tr>
                    <td class="ci-border-b ci-py-3 ci-pl-3">2.</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2">Taxation consulting (hour)</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2 ci-text-right">$60.00</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2 ci-text-center">2</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2 ci-text-center">20%</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2 ci-text-right">$120.00</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2 ci-pr-3 ci-text-right">$144.00</td>
                </tr>
                <tr>
                    <td class="ci-border-b ci-py-3 ci-pl-3">3.</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2">Bookkeeping services</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2 ci-text-right">$50.00</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2 ci-text-center">1</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2 ci-text-center">20%</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2 ci-text-right">$50.00</td>
                    <td class="ci-border-b ci-py-3 ci-pl-2 ci-pr-3 ci-text-right">$60.00</td>
                </tr>
                <tr>
                    <td colspan="7">
                        <table class="ci-w-full ci-border-collapse ci-border-spacing-0">
                            <tbody>
                            <tr>
                                <td class="ci-w-full"></td>
                                <td>
                                    <table class="ci-w-full ci-border-collapse ci-border-spacing-0">
                                        <tbody>
                                        <tr>
                                            <td class="ci-border-b ci-p-3">
                                                <div class="ci-whitespace-nowrap ci-text-slate-400">Net total:</div>
                                            </td>
                                            <td class="ci-border-b ci-p-3 ci-text-right">
                                                <div class="ci-whitespace-nowrap ci-font-bold ci-text-main">$320.00</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ci-p-3">
                                                <div class="ci-whitespace-nowrap ci-text-slate-400">VAT total:</div>
                                            </td>
                                            <td class="ci-p-3 ci-text-right">
                                                <div class="ci-whitespace-nowrap ci-font-bold ci-text-main">$64.00</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ci-bg-main ci-p-3">
                                                <div class="ci-whitespace-nowrap ci-font-bold ci-text-white">Total:</div>
                                            </td>
                                            <td class="ci-bg-main ci-p-3 ci-text-right">
                                                <div class="ci-whitespace-nowrap ci-font-bold ci-text-white">$384.00</div>
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
            <p class="ci-text-main ci-font-bold">PAYMENT DETAILS</p>
            <p>Banks of Banks</p>
            <p>Bank/Sort Code: 1234567</p>
            <p>Account Number: 123456678</p>
            <p>Payment Reference: BRA-00335</p>
        </div>

        <div class="ci-px-14 ci-py-10 ci-text-sm ci-text-neutral-700">
            <p class="ci-text-main ci-font-bold">Notes</p>
            <p class="ci-italic">Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts and visual mockups.</p>
        </div>
    </div>
</div>