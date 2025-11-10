<?php

namespace App\Enums;

use App\Traits\EnumOptions;
use Filament\Support\Contracts\HasLabel;

enum BillingType: string implements HasLabel
{
    use EnumOptions;

    case ONE_TIME = 'one_time';
    case RECURRING = 'recurring';

    public function getLabel(): ?string
    {
        // TODO: Implement getLabel() method.
        return match ($this) {
            self::ONE_TIME => 'Sekali Bayar',
            self::RECURRING => 'Berulang'
        };
    }
}
