<?php

namespace App\Filament\Resources\InvoiceSettingResource;

use App\Models\InvoiceSetting;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoiceSettingResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = InvoiceSetting::class;

    protected static ?string $slug = 'invoice-settings';

    protected static ?string $navigationLabel = 'Pengaturan Tagihan';

    protected static ?string $breadcrumb = 'Pengaturan Tagihan';

    public static function getNavigationUrl(): string
    {
        return static::getUrl('edit', ['record' => InvoiceSetting::first()?->getRouteKey()]);
    }

    public static function getPermissionPrefixes(): array
    {
        // TODO: Implement getPermissionPrefixes() method.
        return [
            'view_any',
            'update'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Pengaturan Tagihan')
                    ->description('Pengaturan ini akan digunakan untuk membuat tagihan secara otomatis.')
                    ->columns()
                    ->schema([
                        ToggleButtons::make('setup_auto_recurring_invoice')
                            ->label('Buat Faktur Berulang Otomatis')
                            ->boolean()
                            ->inline()
                            ->columnSpanFull()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set): void {
                                $set('repeat_every_date', null);
                            }),

                        TextInput::make('repeat_every_date')
                            ->label('Faktur Berulang Setiap Tanggal')
                            ->required(fn(callable $get) => $get('setup_auto_recurring_invoice'))
                            ->integer()
                            ->hintIcon('heroicon-o-information-circle', 'Pembuatan tagihan akan dibuat otomatis setiap tanggal yang ditentukan. Misalnya, jika diisi 5, maka tagihan akan dibuat setiap tanggal 5 bulan berikutnya.')
                            ->visible(fn(callable $get) => $get('setup_auto_recurring_invoice'))
                            ->placeholder('Masukkan setiap tanggal faktur berulang dibuat otomatis'),

                        TextInput::make('due_date_after')
                            ->label('Jatuh Tempo Setelah (Layanan Berulang)')
                            ->required()
                            ->integer()
                            ->hintIcon('heroicon-o-information-circle', 'Jatuh tempo tagihan akan dibuat setelah tanggal pembuatan tagihan. Misalnya, jika diisi 7, maka tagihan akan jatuh tempo 7 hari setelah tanggal pembuatan.')
                            ->suffix('hari')
                            ->placeholder('Masukkan jeda hari sebelum layanan berulang jatuh tempo'),

                        TextInput::make('due_date_after_new_service')
                            ->label('Jatuh Tempo Setelah (Layanan Baru)')
                            ->required()
                            ->integer()
                            ->hintIcon('heroicon-o-information-circle', 'Jatuh tempo tagihan akan dibuat setelah tanggal pembuatan tagihan. Misalnya, jika diisi 7, maka tagihan akan jatuh tempo 7 hari setelah tanggal pembuatan.')
                            ->suffix('hari')
                            ->placeholder('Masukkan jeda hari sebelum layanan baru jatuh tempo'),

                        TextInput::make('cancel_after')
                            ->label('Batalkan Setelah')
                            ->required()
                            ->integer()
                            ->hintIcon('heroicon-o-information-circle', 'Tagihan yang tidak dibayar akan dibatalkan otomatis setelah jatuh tempo. Misalnya, jika diisi 14, maka tagihan akan dibatalkan 14 hari setelah jatuh tempo.')
                            ->suffix('hari')
                            ->placeholder('Masukkan jeda hari sebelum faktur dinyatakan dibatalkan'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('repeat_every_date')
                    ->label('Ulangi Setiap Tanggal'),

                TextColumn::make('due_date_after')
                    ->label('Jatuh Tempo Setelah (Layanan Berulang)')
                    ->suffix(' hari'),

                TextColumn::make('due_date_after_new_service')
                    ->label('Jatuh Tempo Setelah (Layanan Baru)')
                    ->suffix(' hari'),

                TextColumn::make('cancel_after')
                    ->label('Batalkan Setelah')
                    ->suffix(' hari')
                    ->tooltip('Tagihan yang tidak dibayar akan dibatalkan otomatis setelah jatuh tempo.'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()->button(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentSettings::route('/'),
            'edit' => Pages\EditPaymentSetting::route('/{record}'),
        ];
    }
}
