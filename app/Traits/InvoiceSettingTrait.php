<?php

namespace App\Traits;

use App\Models\InvoiceSetting;

trait InvoiceSettingTrait
{
    public function setting(): ?InvoiceSetting
    {
        return InvoiceSetting::first();
    }
}