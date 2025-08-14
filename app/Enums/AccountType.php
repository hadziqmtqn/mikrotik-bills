<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AccountType: string implements HasLabel, HasIcon, HasColor
{
    case PRIBADI = 'pribadi';
    case BISNIS = 'bisnis';

    public function getColor(): string|array|null
    {
        // TODO: Implement getColor() method.
        return match ($this) {
            self::PRIBADI => 'warning',
            self::BISNIS => 'success'
        };
    }

    public function getIcon(): ?string
    {
        // TODO: Implement getIcon() method.
        return match ($this) {
            self::PRIBADI => 'heroicon-o-user',
            self::BISNIS => 'heroicon-o-users'
        };
    }

    public function getLabel(): ?string
    {
        // TODO: Implement getLabel() method.
        return match ($this) {
            self::PRIBADI => __('Pribadi'),
            self::BISNIS => __('Bisnis')
        };
    }

    public static function options(): array
    {
        return [
            self::PRIBADI->value => self::PRIBADI->getLabel(),
            self::BISNIS->value => self::BISNIS->getLabel(),
        ];
    }

    public static function colors(): array
    {
        return [
            self::PRIBADI->value => self::PRIBADI->getColor(),
            self::BISNIS->value => self::BISNIS->getColor(),
        ];
    }
}
