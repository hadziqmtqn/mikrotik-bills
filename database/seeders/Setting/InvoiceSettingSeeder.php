<?php

namespace Database\Seeders\Setting;

use App\Models\InvoiceSetting;
use Illuminate\Database\Seeder;

class InvoiceSettingSeeder extends Seeder
{
    public function run(): void
    {
        $paymentSetting = new InvoiceSetting();
        $paymentSetting->repeat_every_date = 5; // Buat invoice baru setiap tanggal 5
        $paymentSetting->due_date_after = 20; // Jatuh tempo setiap tanggal 20
        $paymentSetting->cancel_after = 7; // Batalkan invoice setelah 7 hari dari tanggal jatuh tempo
        $paymentSetting->save();
    }
}
