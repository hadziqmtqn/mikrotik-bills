<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusData: string implements HasColor, HasLabel
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case SUSPENDED = 'suspended';
    case CANCELLED = 'cancelled';

    public function getLabel(): ?string
    {
        // TODO: Implement getLabel() method.
        return match ($this) {
            self::PENDING => 'Pending',
            self::PAID => 'Paid',
            self::SUSPENDED => 'Suspended',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function getColor(): string|array|null
    {
        // TODO: Implement getColor() method.
        return match ($this) {
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::SUSPENDED, self::CANCELLED => 'danger',
        };
    }
}
