<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Carbon;

class DateHelper
{
    /**
     * @param $date
     * @param string $format
     * @param string $default
     * @param string $locale
     * @return string
     */
    public static function indonesiaDate($date, string $format = 'D MMMM Y', string $default = '-', string $locale = 'id_ID'): string
    {
        if (empty($date)) {
            return $default;
        }

        try {
            $carbon = ($date instanceof Carbon) ? $date : Carbon::parse($date);
            return $carbon->locale($locale)->isoFormat($format);
        } catch (Exception $e) {
            return $default;
        }
    }
}