<?php

namespace Database\Seeders\Service;

use App\Models\ServicePackage;
use Illuminate\Database\Seeder;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\UnavailableStream;

class ServicePackageSeeder extends Seeder
{
    /**
     * @throws UnavailableStream
     * @throws InvalidArgument
     * @throws Exception
     */
    public function run(): void
    {
        $rows = Reader::createFromPath(database_path('import/service_packages.csv'))
            ->setHeaderOffset(0)
            ->setDelimiter(';');

        foreach ($rows as $row) {
            $servicePackage = new ServicePackage();
            $servicePackage->service_type = $row['service_type'];
            $servicePackage->package_name = $row['package_name'];
            $servicePackage->payment_type = $row['payment_type'];
            $servicePackage->plan_type = $row['plan_type'];
            $servicePackage->package_limit_type = !empty($row['package_limit_type']) ? $row['package_limit_type'] : null;
            $servicePackage->limit_type = !empty($row['limit_type']) ? $row['limit_type'] : null;
            $servicePackage->time_limit = !empty($row['time_limit']) ? $row['time_limit'] : null;
            $servicePackage->time_limit_unit = !empty($row['time_limit_unit']) ? $row['time_limit_unit'] : null;
            $servicePackage->data_limit = !empty($row['data_limit']) ? $row['data_limit'] : null;
            $servicePackage->data_limit_unit = !empty($row['data_limit_unit']) ? $row['data_limit_unit'] : null;
            $servicePackage->validity_period = !empty($row['validity_period']) ? $row['validity_period'] : null;
            $servicePackage->validity_unit = !empty($row['validity_unit']) ? $row['validity_unit'] : null;
            $servicePackage->daily_price = !empty($row['daily_price']) ? $row['daily_price'] : null;
            $servicePackage->package_price = $row['package_price'];
            $servicePackage->price_before_discount = !empty($row['price_before_discount']) ? $row['price_before_discount'] : null;
            $servicePackage->router_id = $row['router_id'];
            $servicePackage->description = !empty($row['description']) ? $row['description'] : null;
            $servicePackage->save();
        }
    }
}
