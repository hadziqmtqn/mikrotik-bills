<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\Schemas\InvoiceForm;
use App\Filament\Resources\InvoiceResource\Schemas\InvoiceTable;
use App\Models\Invoice;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Invoice::class;
    protected static ?string $slug = 'invoices';
    protected static ?string $navigationGroup = 'Invoice';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Faktur';
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    public static function getPermissionPrefixes(): array
    {
        // TODO: Implement getPermissionPrefixes() method.
        return [
            'view_any',
            'view',
            'create',
            'update',
            'delete',
        ];
    }

    public static function form(Form $form): Form
    {
        return InvoiceForm::form($form);
    }

    public static function table(Table $table): Table
    {
        return InvoiceTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'user.name'];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'invoiceItems.customerService'])
            ->whereHas('invoiceItems');
    }
}
