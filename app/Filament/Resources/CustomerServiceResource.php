<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerServiceResource\Pages;
use App\Filament\Resources\CustomerServiceResource\Schemas\CustomerServiceForm;
use App\Filament\Resources\CustomerServiceResource\Schemas\CustomerServiceTable;
use App\Models\CustomerService;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Exception;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerServiceResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = CustomerService::class;
    protected static ?string $slug = 'customer-services';
    protected static ?string $navigationLabel = 'Layanan Pelanggan';
    protected static ?string $navigationIcon = 'heroicon-o-server';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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
        return CustomerServiceForm::form($form);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return CustomerServiceTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerServices::route('/'),
            'create' => Pages\CreateCustomerService::route('/create'),
            'view' => Pages\ViewCustomerService::route('/{record}'),
            'edit' => Pages\EditCustomerService::route('/{record}/edit'),
            'invoice_history' => Pages\InvoiceHistory::route('/{record}/invoice-history'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('user.userProfile', 'servicePackage')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->reference_number ?? 'Layanan Pelanggan';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'reference_number',
            'user.name',
            'servicePackage.package_name',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Nama Pelanggan' => $record->user?->name,
            'Paket' => $record->servicePackage?->package_name,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'servicePackage']);
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return CustomerServiceResource::getUrl('view', ['record' => $record]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewCustomerService::class,
            Pages\EditCustomerService::class,
            Pages\InvoiceHistory::class
        ]);
    }
}
