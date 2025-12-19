<?php

namespace App\Enums;

use App\Traits\EnumOptions;
use Filament\Support\Contracts\HasLabel;

enum TimeLimitType: string implements HasLabel
{
    use EnumOptions;

    case MENIT = 'menit';
    case JAM = 'jam';
    case HARI = 'hari';
    case BULAN = 'bulan';

    /**
     * - Paket layanan Hostpot => Menit, Jam, Hari
     * - Paket layanan PPoE => Bulan
    */

    public function getLabel(): ?string
    {
        // TODO: Implement getLabel() method.
        return match ($this) {
            self::MENIT => 'Menit',
            self::JAM => 'Jam',
            self::HARI => 'Hari',
            self::BULAN => 'Bulan'
        };
    }
}
