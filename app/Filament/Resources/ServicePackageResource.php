<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicePackageResource\Pages;
use App\Models\ServicePackage;
use Filament\Forms\Components\Checkbox;
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

class ServicePackageResource extends Resource
{
    protected static ?string $model = ServicePackage::class;

    protected static ?string $slug = 'service-packages';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('serial_number')
                    ->required()
                    ->integer(),

                TextInput::make('code')
                    ->required(),

                TextInput::make('service_type')
                    ->required(),

                TextInput::make('package_name')
                    ->required(),

                TextInput::make('payment_type')
                    ->required(),

                TextInput::make('plan_type')
                    ->required(),

                TextInput::make('package_limit_type'),

                TextInput::make('limit_type'),

                TextInput::make('time_limit')
                    ->integer(),

                TextInput::make('time_limit_unit'),

                TextInput::make('data_limit')
                    ->integer(),

                TextInput::make('data_limit_unit'),

                TextInput::make('validity_period')
                    ->integer(),

                TextInput::make('validity_unit'),

                TextInput::make('package_price')
                    ->required()
                    ->numeric(),

                TextInput::make('price_before_discount')
                    ->numeric(),

                TextInput::make('router_id')
                    ->required()
                    ->integer(),

                TextInput::make('description'),

                Checkbox::make('is_active'),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?ServicePackage $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?ServicePackage $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('serial_number'),

                TextColumn::make('code'),

                TextColumn::make('service_type'),

                TextColumn::make('package_name'),

                TextColumn::make('payment_type'),

                TextColumn::make('plan_type'),

                TextColumn::make('package_limit_type'),

                TextColumn::make('limit_type'),

                TextColumn::make('time_limit'),

                TextColumn::make('time_limit_unit'),

                TextColumn::make('data_limit'),

                TextColumn::make('data_limit_unit'),

                TextColumn::make('validity_period'),

                TextColumn::make('validity_unit'),

                TextColumn::make('package_price'),

                TextColumn::make('price_before_discount'),

                TextColumn::make('router_id'),

                TextColumn::make('description'),

                TextColumn::make('is_active'),
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
            'index' => Pages\ListServicePackages::route('/'),
            'create' => Pages\CreateServicePackage::route('/create'),
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

    public static function getGloballySearchableAttributes(): array
    {
        return ['slug'];
    }
}
