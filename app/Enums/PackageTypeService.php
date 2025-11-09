<?php

namespace App\Enums;

use App\Traits\EnumOptions;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PackageTypeService: string implements HasLabel, HasColor
{
    use EnumOptions;

    case SUBSCRIPTION = 'subscription';
    case ONE_TIME = 'one-time';

    public function getLabel(): string
    {
        return match ($this) {
            self::SUBSCRIPTION => 'Langganan',
            self::ONE_TIME => 'Sekali Bayar',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::SUBSCRIPTION => 'primary',
            self::ONE_TIME => 'warning',
        };
    }

    public static function colors(): array
    {
        return [
            self::SUBSCRIPTION->value => self::SUBSCRIPTION->getColor(),
            self::ONE_TIME->value => self::ONE_TIME->getColor(),
        ];
    }
}
