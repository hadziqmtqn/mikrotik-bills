<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ValidityUnit: string implements HasLabel
{
    case MENIT = 'menit';
    case JAM = 'jam';
    case HARI = 'hari';
    case BULAN = 'bulan';

    public function getLabel(): ?string
    {
        // TODO: Implement getLabel() method.
        return match ($this) {
            self::MENIT => 'Menit',
            self::JAM => 'Jam',
            self::HARI => 'Hari',
            self::BULAN => 'Bulan',
        };
    }

    public static function options(): array
    {
        return [
            self::MENIT->value => self::MENIT->getLabel(),
            self::JAM->value => self::JAM->getLabel(),
            self::HARI->value => self::HARI->getLabel(),
            self::BULAN->value => self::BULAN->getLabel(),
        ];
    }
}
