<?php

namespace App\Filament\Resources\CustomerServiceResource;

use App\Filament\Resources\CustomerServiceResource\Pages\ManageCustomerServiceUsage;
use App\Filament\Resources\CustomerServiceResource\Pages\ManageInvoiceHistory;
use App\Filament\Resources\CustomerServiceResource\RelationManagers\AdditionalServiceFeesRelationManager;
use App\Filament\Resources\CustomerServiceResource\Schemas\CustomerServiceForm;
use App\Filament\Resources\CustomerServiceResource\Tables\CustomerServiceTable;
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

    protected static ?string $breadcrumb = 'Layanan Pelanggan';

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
            'usage' => ManageCustomerServiceUsage::route('/{record}/usages'),
            'invoice_history' => ManageInvoiceHistory::route('/{record}/invoice-history'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'user.userProfile',
                'servicePackage',
                'invCustomerServices',
                'additionalServiceFees.extraCost'
            ])
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
            'servicePackage:id,service_type,package_name',
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
            ManageCustomerServiceUsage::class,
            ManageInvoiceHistory::class
        ]);
    }

    public static function getRelations(): array
    {
        return [
            AdditionalServiceFeesRelationManager::class
        ];
    }
}
