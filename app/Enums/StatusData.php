<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusData: string implements HasColor, HasLabel
{
    case PENDING = 'pending';
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case CANCELLED = 'cancelled';
    case OVERDUE = 'overdue';
    case PARTIALLY_PAID = 'partially_paid';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::UNPAID => 'Unpaid',
            self::PAID => 'Paid',
            self::ACTIVE => 'Active',
            self::SUSPENDED => 'Suspended',
            self::CANCELLED => 'Cancelled',
            self::OVERDUE => 'Overdue',
            self::PARTIALLY_PAID => 'Partially Paid',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING, self::UNPAID, self::OVERDUE, self::PARTIALLY_PAID => 'warning',
            self::PAID, self::ACTIVE => 'primary',
            self::SUSPENDED, self::CANCELLED => 'danger',
        };
    }

    public function htmlColor(): string
    {
        return match ($this) {
            self::PENDING, self::UNPAID, self::OVERDUE, self::PARTIALLY_PAID => '#ffc107',
            self::PAID, self::ACTIVE => '#007bff',
            self::SUSPENDED, self::CANCELLED => '#dc3545',
        };
    }

    public static function options(array $cases = []): array
    {
        $allCases = self::cases();

        // Jika $cases kosong, tampilkan semua
        if (empty($cases)) {
            $casesToShow = $allCases;
        } else {
            $casesToShow = array_filter($allCases, function($case) use ($cases) {
                // Cek apakah enum atau value ada di $cases
                return in_array($case, $cases, true) || in_array($case->value, $cases, true);
            });
        }

        return collect($casesToShow)
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }

    public static function colors(array $cases = []): array
    {
        $allCases = self::cases();

        // Jika $cases kosong, tampilkan semua
        if (empty($cases)) {
            $casesToShow = $allCases;
        } else {
            $casesToShow = array_filter($allCases, function($case) use ($cases) {
                // Cek apakah enum atau value ada di $cases
                return in_array($case, $cases, true) || in_array($case->value, $cases, true);
            });
        }

        return collect($casesToShow)
            ->mapWithKeys(fn($case) => [$case->value => $case->getColor()])
            ->toArray();
    }
}