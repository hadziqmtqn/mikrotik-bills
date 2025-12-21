<?php

namespace App\Services;

use App\Models\InvoiceSetting;
use Illuminate\Support\Carbon;

class InvoiceSettingService
{
    public static function repeatitionDate(): Carbon
    {
        $invoiceSetting = InvoiceSetting::first();
        $now = Carbon::now();

        return Carbon::createFromDate($now->year, $now->month, ($invoiceSetting?->repeat_every_date ?? 5));
    }

    public static function nextRepetitionDate(): Carbon
    {
        /**
         * Misal bulan ini tagihan dibuat setiap tanggal 5, maka tambahkan 1 bulan yang akan datang untuk mencatat tagihan bulan depan
        */
        return self::repeatitionDate()->addMonth();
    }
}
