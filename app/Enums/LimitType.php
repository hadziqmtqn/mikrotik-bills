<?php

namespace App\Enums;

use App\Traits\EnumOptions;
use Filament\Support\Contracts\HasLabel;

enum LimitType: string implements HasLabel
{
    use EnumOptions;

    case TIME = 'time';
    case DATA = 'data';
    case BOTH = 'both';

    public function getLabel(): ?string
    {
        // TODO: Implement getLabel() method.
        return match ($this) {
            self::TIME => 'Waktu',
            self::DATA => 'Data',
            self::BOTH => 'Waktu & Data'
        };
    }
}
