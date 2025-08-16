<?php

namespace Database\Seeders\Reference;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\UnavailableStream;

class BankAccountSeeder extends Seeder
{
    /**
     * @throws UnavailableStream
     * @throws InvalidArgument
     * @throws Exception
     */
    public function run(): void
    {
        $rows = Reader::createFromPath(database_path('import/bank-account.csv'))
            ->setHeaderOffset(0)
            ->setDelimiter(';');

        foreach ($rows as $row) {
            $bankAccount = new BankAccount();
            $bankAccount->bank_name = $row['bank_name'];
            $bankAccount->short_name = $row['short_name'];
            $bankAccount->account_number = $row['account_number'];
            $bankAccount->account_name = $row['account_name'];
            $bankAccount->save();
        }
    }
}
