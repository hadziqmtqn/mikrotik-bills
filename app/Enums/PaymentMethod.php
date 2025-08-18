<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel, HasColor, HasIcon
{
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';

    public function getLabel(): ?string
    {
        // TODO: Implement getLabel() method.
        return match ($this) {
            self::CASH => 'Cash',
            self::BANK_TRANSFER => 'Bank Transfer',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::CASH => 'primary',
            self::BANK_TRANSFER => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::CASH => 'heroicon-s-credit-card',
            self::BANK_TRANSFER => 'heroicon-s-wallet',
        };
    }

    public static function options(): array
    {
        return [
            self::CASH->value => self::CASH->getLabel(),
            self::BANK_TRANSFER->value => self::BANK_TRANSFER->getLabel(),
        ];
    }

    public static function colors(): array
    {
        return [
            self::CASH->value => self::CASH->getColor(),
            self::BANK_TRANSFER->value => self::BANK_TRANSFER->getColor(),
        ];
    }

    public static function icons(): array
    {
        return [
            self::CASH->value => self::CASH->getIcon(),
            self::BANK_TRANSFER->value => self::BANK_TRANSFER->getIcon(),
        ];
    }
}
