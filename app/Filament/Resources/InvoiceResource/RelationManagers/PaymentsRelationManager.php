<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Enums\PaymentMethod;
use App\Enums\StatusData;
use App\Filament\Resources\PaymentResource\Schemas\PaymentForm;
use App\Helpers\DateHelper;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager implements HasShieldPermissions
{
    protected static string $relationship = 'payments';
    protected static ?string $recordTitleAttribute = 'number';

    public static function getPermissionPrefixes(): array
    {
        // TODO: Implement getPermissionPrefixes() method.
        return [
            'view_any',
            'create',
        ];
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return PaymentForm::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_id')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Tgl. Bayar')
                    ->date()
                    ->formatStateUsing(fn ($state): string => DateHelper::indonesiaDate($state, 'D MMM Y')),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->formatStateUsing(fn ($state): string => PaymentMethod::tryFrom($state)?->getLabel() ?? 'N/A'),

                Tables\Columns\TextColumn::make('bankAccount.short_name')
                    ->label('Bank Tujuan'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => StatusData::tryFrom($state)?->getColor() ?? 'gray')
                    ->formatStateUsing(fn ($state): string => StatusData::tryFrom($state)?->getLabel() ?? 'N/A')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->createAnother(false)
                    ->visible(fn() => $this->getOwnerRecord()->status === StatusData::UNPAID->value && $this->getOwnerRecord()?->payments()->count() === 0)
                    ->closeModalByClickingAway(false)
                    ->mutateFormDataUsing(function (array $data) {
                        $data['user_id'] = $this->getOwnerRecord()->user_id;
                        $data['invoice_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    })
            ])
            ->filters([
                //Tables\Filters\TrashedFilter::make()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                /*Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make()*/
            ])
            ->bulkActions([
                /*Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),*/
            ]);
    }
}
