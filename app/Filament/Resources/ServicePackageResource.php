<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicePackageResource\Pages;
use App\Filament\Resources\ServicePackageResource\Schemas\ServicePackageForm;
use App\Filament\Resources\ServicePackageResource\Schemas\ServicePackageTable;
use App\Models\ServicePackage;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Exception;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServicePackageResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = ServicePackage::class;
    protected static ?string $slug = 'service-packages';
    protected static ?string $navigationLabel = 'Paket Layanan';
    protected static ?string $navigationGroup = 'Service';
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';
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
        return ServicePackageForm::form($form);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return ServicePackageTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServicePackages::route('/'),
            'create' => Pages\CreateServicePackage::route('/create'),
            'view' => Pages\ViewServicePackage::route('/{record}'),
            'edit' => Pages\EditServicePackage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewServicePackage::class,
            Pages\EditServicePackage::class,
        ]);
    }
}
