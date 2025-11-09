<?php

namespace App\Enums;

use App\Traits\EnumOptions;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusData: string implements HasColor, HasLabel
{
    use EnumOptions;

    case PENDING = 'pending';
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case CANCELLED = 'cancelled';
    case OVERDUE = 'overdue';
    case PARTIALLY_PAID = 'partially_paid';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Tertunda',
            self::UNPAID => 'Tidak Lunas',
            self::PAID => 'Lunas',
            self::ACTIVE => 'Aktif',
            self::INACTIVE => 'Tidak Aktif',
            self::SUSPENDED => 'Ditangguhkan',
            self::CANCELLED => 'Dibatalkan',
            self::OVERDUE => 'Kadaluarsa',
            self::PARTIALLY_PAID => 'Bayar Sebagian',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING, self::UNPAID, self::OVERDUE, self::PARTIALLY_PAID => 'warning',
            self::PAID, self::ACTIVE => 'primary',
            self::SUSPENDED, self::CANCELLED, self::INACTIVE => 'danger',
        };
    }

    public function htmlColor(): string
    {
        return match ($this) {
            self::PENDING, self::UNPAID, self::OVERDUE, self::PARTIALLY_PAID => '#ffc107',
            self::PAID, self::ACTIVE => '#007bff',
            self::SUSPENDED, self::CANCELLED, self::INACTIVE => '#dc3545',
        };
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
