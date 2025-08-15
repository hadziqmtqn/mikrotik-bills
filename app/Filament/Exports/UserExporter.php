<?php

namespace App\Filament\Exports;

use App\Enums\AccountType;
use App\Helpers\DateHelper;
use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\Style;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Nama'),

            ExportColumn::make('userProfile.whatsapp_number')
                ->label('No. WhatsApp'),

            ExportColumn::make('email')
                ->label('Email'),

            ExportColumn::make('userProfile.account_type')
                ->label('Tipe Akun')
                ->formatStateUsing(fn($state): string => AccountType::tryFrom($state)?->getLabel() ?? 'N/A'),

            ExportColumn::make('userProfile.activation_date')
                ->label('Tgl. Aktivasi')
                ->formatStateUsing(fn($state): string => DateHelper::indonesiaDate($state)),

            ExportColumn::make('userProfile.ppoe_name')
                ->label('Nama PPPoE'),

            ExportColumn::make('userProfile.street')
                ->label('Jalan'),

            ExportColumn::make('userProfile.street')
                ->label('Jalan'),

            ExportColumn::make('userProfile.village')
                ->label('Desa'),

            ExportColumn::make('userProfile.district')
                ->label('Kecamatan'),

            ExportColumn::make('userProfile.city')
                ->label('Kota/Kab'),

            ExportColumn::make('userProfile.province')
                ->label('Provinsi'),

            ExportColumn::make('userProfile.postal_code')
                ->label('Kode Pos'),

            ExportColumn::make('is_active')
                ->label('Status Akun')
                ->formatStateUsing(fn($state): string => $state ? 'Aktif' : 'Tidak Aktif'),
        ];
    }

    public function getXlsxHeaderCellStyle(): ?Style
    {
        return (new Style())
            ->setFontSize(14)
            ->setFontName('Arial')
            ->setBorder(new Border());
    }

    public function getXlsxCellStyle(): ?Style
    {
        return (new Style())
            ->setFontSize(12)
            ->setFontName('Arial')
            ->setBorder(new Border());
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your user export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
