<?php

namespace App\Helpers;

class IdrCurrency
{
    public static function convert($nominal): string
    {
        return 'Rp ' . number_format($nominal,2,',','.');
    }
}