<?php

namespace App\Enums;

use App\Traits\EnumOptions;
use Filament\Support\Contracts\HasLabel;

enum PaymentType: string implements HasLabel
{
    use EnumOptions;

    case PREPAID = 'prepaid';
    case POSTPAID = 'postpaid';

    public function getLabel(): ?string
    {
        // TODO: Implement getLabel() method.
        return match ($this) {
            self::PREPAID => 'Prabayar',
            self::POSTPAID => 'Pascabayar'
        };
    }
}
