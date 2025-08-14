<?php

namespace App\Filament\Resources\UserResource\Schemas;

use App\Enums\AccountType;
use App\Models\User;
use Exception;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserTable
{
    /**
     * @throws Exception
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->description(fn($record): string => $record->userProfile?->place_name ?? '-')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('userProfile.account_type')
                    ->label('Tipe Akun')
                    ->badge()
                    ->color(fn($state): ?string => AccountType::tryFrom($state)?->getColor() ?? 'secondary')
                    ->icon(fn($state): ?string => AccountType::tryFrom($state)?->getIcon() ?? null)
                    ->formatStateUsing(fn($state): ?string => AccountType::tryFrom($state)?->getLabel() ?? 'Unknown')
                    ->sortable(),

                TextColumn::make('userProfile.whatsapp_number')
                    ->label('No. WhatsApp')
                    ->searchable(),

                TextColumn::make('userProfile.street')
                    ->label('Alamat')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn($record): string => $record->userProfile?->street ?? '-'),

                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn($state): string => match ($state) {
                        'super_admin' => 'primary',
                        'admin' => 'info',
                        'user' => 'gray',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn($state): string => ucwords(str_replace('_', ' ', $state)))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('account_type')
                    ->label('Tipe Akun')
                    ->options(AccountType::options())
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->whereHas('userProfile', function ($q) use ($data) {
                                $q->where('account_type', $data['value']);
                            });
                        }
                    })
                    ->native(false),

                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->where('is_active', ($data['value'] === 'active'));
                        }
                    })
                    ->native(false),

                TrashedFilter::make()
                    ->label('Data Terhapus')
                    ->native(false),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->closeModalByClickingAway(false),
                    DeleteAction::make()
                        ->hidden(fn(User $record): bool => $record->hasRole('super_admin')),
                    RestoreAction::make(),
                    ForceDeleteAction::make()
                        ->hidden(fn(User $record): bool => $record->hasRole('super_admin')),
                ])
                    ->link()
                    ->label('Actions')
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}