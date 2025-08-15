<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ServiceType: string implements HasLabel, HasColor
{
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

    public static function options(): array
    {
        return [
            self::HOTSPOT->value => self::HOTSPOT->getLabel(),
            self::PPPOE->value => self::PPPOE->getLabel(),
        ];
    }
}
