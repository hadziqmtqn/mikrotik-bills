<?php

namespace App\Enums;

use App\Traits\EnumOptions;
use Filament\Support\Contracts\HasLabel;

enum PackageLimitType: string implements HasLabel
{
    use EnumOptions;

    case UNLIMITED = 'unlimited';
    case LIMITED = 'limited';

    public function getLabel(): ?string
    {
        // TODO: Implement getLabel() method.
        return match ($this) {
            self::UNLIMITED => 'Tak Terbatas',
            self::LIMITED => 'Terbatas'
        };
    }
}
