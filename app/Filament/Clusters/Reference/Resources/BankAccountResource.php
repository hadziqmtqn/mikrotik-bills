<?php

namespace App\Filament\Clusters\Reference\Resources;

use App\Filament\Clusters\Reference\Resources\BankAccountResource\Pages\ListBankAccounts;
use App\Filament\Clusters\ReferenceCluster;
use App\Models\BankAccount;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Exception;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BankAccountResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = BankAccount::class;


    protected static ?string $slug = 'bank-accounts';

    protected static ?string $navigationLabel = 'Rekening Bank';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $cluster = ReferenceCluster::class;

    protected static ?int $navigationSort = 2;

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
                TextInput::make('bank_name')
                    ->label('Nama Bank')
                    ->required()
                    ->placeholder('Contoh: Bank Central Asia'),

                TextInput::make('short_name')
                    ->label('Singkatan')
                    ->maxLength(10)
                    ->placeholder('Contoh: BCA'),

                TextInput::make('account_number')
                    ->label('No. Rekening')
                    ->required()
                    ->placeholder('Contoh: 1234567890'),

                TextInput::make('account_name')
                    ->label('Atas Nama')
                    ->required()
                    ->placeholder('Contoh: John Doe'),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->inline(false),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bank_name')
                    ->label('Nama Bank')
                    ->searchable()
                    ->formatStateUsing(fn($state, ?BankAccount $bankAccount): string => $state . ($bankAccount->short_name ? ' (' . $bankAccount->short_name . ')' : '')),

                TextColumn::make('account_number')
                    ->label('No. Rekening')
                    ->searchable(),

                TextColumn::make('account_name')
                    ->label('Atas Nama')
                    ->searchable(),

                ToggleColumn::make('is_active')
                    ->label('Status')
                    ->sortable()
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make()->native(false),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()->modalHeading('Ubah Rekening Bank'),
                    DeleteAction::make()->modalHeading('Hapus rekening bank'),
                    RestoreAction::make()->modalHeading('Pulihkan data'),
                    ForceDeleteAction::make()->modalHeading('Hapus selamanya'),
                ])
                ->button()
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBankAccounts::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
