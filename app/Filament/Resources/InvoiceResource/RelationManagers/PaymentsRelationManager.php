<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use App\Filament\Resources\PaymentResource\Schemas\PaymentForm;
use App\Models\Invoice;
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
                Tables\Columns\TextColumn::make('invoice_id'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->createAnother(false)
                    ->visible(fn() => $this->getOwnerRecord()?->payments()->count() === 0)
                    ->closeModalByClickingAway(false)
                    ->mutateFormDataUsing(function (): array {
                        return [
                            'user_id' => $this->getOwnerRecord()->user_id,
                            'invoice_id' => $this->getOwnerRecord()->id,
                        ];
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
