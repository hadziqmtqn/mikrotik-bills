<?php

namespace Database\Seeders\Service;

use App\Enums\BillingType;
use App\Models\ExtraCost;
use Illuminate\Database\Seeder;

class ExtraCostSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->items() as $item) {
            $extraCost = new ExtraCost();
            $extraCost->name = $item['name'];
            $extraCost->fee = $item['fee'];
            $extraCost->billing_type = $item['billing_type'];
            $extraCost->save();
        }
    }

    private function items(): array
    {
        return [
            [
                'name' => 'Pasang Baru',
                'fee' => 100000,
                'billing_type' => BillingType::ONE_TIME->value
            ],
            [
                'name' => 'Sewa Perangkat',
                'fee' => 30000,
                'billing_type' => BillingType::RECURRING->value
            ],
            [
                'name' => 'Materai',
                'fee' => 10000,
                'billing_type' => BillingType::RECURRING->value
            ],
        ];
    }
}
