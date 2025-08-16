<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerServiceResource\Pages;
use App\Filament\Resources\CustomerServiceResource\Schemas\CustomerServiceForm;
use App\Filament\Resources\CustomerServiceResource\Schemas\CustomerServiceTable;
use App\Models\CustomerService;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerServiceResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = CustomerService::class;
    protected static ?string $slug = 'customer-services';
    protected static ?string $navigationLabel = 'Layanan Pelanggan';
    protected static ?string $navigationGroup = 'Service';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-server';

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

    public static function table(Table $table): Table
    {
        return CustomerServiceTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerServices::route('/'),
            'create' => Pages\CreateCustomerService::route('/create'),
            'edit' => Pages\EditCustomerService::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['slug'];
    }
}
