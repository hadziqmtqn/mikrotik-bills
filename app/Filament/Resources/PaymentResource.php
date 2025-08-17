<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
    
class PaymentResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Payment::class;
    protected static ?string $slug = 'payments';
    protected static ?string $title = 'Pembayaran';
    protected static ?string $navigationGroup = 'Payment';
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function getPermissionPrefixes(): array
    {
        // TODO: Implement getPermissionPrefixes() method.
        return [
            'view_any',
            'view',
            'create',
            'update',
            'delete',
            'restore',
            'force_delete',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('serial_number')
                    ->required()
                    ->integer(),

                TextInput::make('code')
                    ->required(),

                TextInput::make('user_id')
                    ->required()
                    ->integer(),

                TextInput::make('invoice_id')
                    ->required()
                    ->integer(),

                TextInput::make('payment_method')
                    ->required(),

                TextInput::make('bank_account_id')
                    ->integer(),

                DatePicker::make('date'),

                TextInput::make('status')
                    ->required(),

                TextInput::make('notes'),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?Payment $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?Payment $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('serial_number'),

                TextColumn::make('code'),

                TextColumn::make('user_id'),

                TextColumn::make('invoice_id'),

                TextColumn::make('payment_method'),

                TextColumn::make('bank_account_id'),

                TextColumn::make('date')
                    ->date(),

                TextColumn::make('status'),

                TextColumn::make('notes'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
                ForceDeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'invoice', 'bankAccount'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'invoice.code'];
    }
}
