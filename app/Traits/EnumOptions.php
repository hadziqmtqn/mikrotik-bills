<?php

namespace App\Traits;

use UnitEnum;

trait EnumOptions
{
    public static function options(array $cases = []): array
    {
        $allCases = self::cases();

        // Jika $cases kosong, tampilkan semua
        if (empty($cases)) {
            $casesToShow = $allCases;
        } else {
            $casesToShow = array_filter($allCases, function($case) use ($cases) {
                // Cek apakah enum atau value ada di $cases
                return in_array($case, $cases, true) || in_array($case->value, $cases, true);
            });
        }

        return collect($casesToShow)
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }

    /**
     * Return just the enum values (strings) for the given cases or all cases.
     *
     * @param array $cases Array of enum instances or values to filter by (optional)
     * @return array<int,string>
     */
    public static function values(array $cases = []): array
    {
        $allCases = self::cases();

        if (empty($cases)) {
            $casesToShow = $allCases;
        } else {
            $casesToShow = array_filter($allCases, function ($case) use ($cases) {
                return in_array($case, $cases, true) || in_array($case->value, $cases, true);
            });
        }

        return array_map(fn($case) => $case->value, $casesToShow);
    }

    /**
     * Return enum case instances (objects) for the given cases or all cases.
     * Useful when you need the enum objects themselves.
     *
     * @param array $cases
     * @return array<int,UnitEnum>
     */
    public static function casesArray(array $cases = []): array
    {
        $allCases = self::cases();

        if (empty($cases)) {
            return $allCases;
        }

        return array_values(array_filter($allCases, function ($case) use ($cases) {
            return in_array($case, $cases, true) || in_array($case->value, $cases, true);
        }));
    }
}
