<?php

namespace App\Filament\Clusters\Reference\Resources;

use App\Filament\Clusters\Reference\Resources\ExtraCostResource\Schemas\ExtraCostForm;
use App\Filament\Clusters\Reference\Resources\ExtraCostResource\Tables\ExtraCostTable;
use App\Filament\Clusters\ReferenceCluster;
use App\Filament\Clusters\Reference\Resources\ExtraCostResource\Pages;
use App\Models\ExtraCost;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ExtraCostResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = ExtraCost::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Biaya Tambahan';

    protected static ?string $breadcrumb = 'Biaya Tambahan';

    protected static ?string $cluster = ReferenceCluster::class;

    protected static ?int $navigationSort = 1;

    public static function getPermissionPrefixes(): array
    {
        // TODO: Implement getPermissionPrefixes() method.
        return [
            'view_any',
            'view',
            'create',
            'update',
            'delete'
        ];
    }

    public static function form(Form $form): Form
    {
        return ExtraCostForm::form($form);
    }

    public static function table(Table $table): Table
    {
        return ExtraCostTable::table($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExtraCosts::route('/'),
        ];
    }
}
