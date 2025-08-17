<?php

namespace App\Console\Commands;

use App\Enums\StatusData;
use App\Models\Invoice;
use Illuminate\Console\Command;

class InvoiceCancalledCommand extends Command
{
    protected $signature = 'invoice:cancalled';

    protected $description = 'Command description';

    public function handle(): void
    {
        Invoice::where('cancel_date', '<', now()->toDateTimeString())
            ->where('status', StatusData::OVERDUE->value)
            ->update(['status' => StatusData::CANCELLED->value]);
    }
}
