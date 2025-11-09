<?php

namespace App\Enums;

use App\Traits\EnumOptions;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ServiceType: string implements HasLabel, HasColor
{
    use EnumOptions;

    case HOTSPOT = 'hotspot';
    case PPPOE = 'pppoe';

    public function getLabel(): ?string
    {
        // TODO: Implement getLabel() method.
        return match ($this) {
            self::HOTSPOT => 'Hotspot',
            self::PPPOE => 'PPPoE',
        };
    }

    public function getColor(): string|array|null
    {
        // TODO: Implement getColor() method.
        return match ($this) {
            self::HOTSPOT => 'primary',
            self::PPPOE => 'danger',
        };
    }
}
