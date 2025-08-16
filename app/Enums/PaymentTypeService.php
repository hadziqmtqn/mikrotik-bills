<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentTypeService: string implements HasLabel, HasColor
{
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

    public static function options(): array
    {
        return [
            self::SUBSCRIPTION->value => self::SUBSCRIPTION->getLabel(),
            self::ONE_TIME->value => self::ONE_TIME->getLabel(),
        ];
    }

    public static function colors(): array
    {
        return [
            self::SUBSCRIPTION->value => self::SUBSCRIPTION->getColor(),
            self::ONE_TIME->value => self::ONE_TIME->getColor(),
        ];
    }
}
