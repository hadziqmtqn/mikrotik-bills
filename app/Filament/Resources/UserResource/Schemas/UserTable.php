<?php

namespace App\Filament\Resources\UserResource\Schemas;

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
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

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

                TextColumn::make('userProfile.whatsapp_number')
                    ->label('WhatsApp Number')
                    ->searchable(),

                TextColumn::make('userProfile.street')
                    ->label('Street')
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
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('role')
                    ->label('Role')
                    ->options(fn () => Role::all()->pluck('name', 'id'))
                    ->default(Role::where('name', 'user')->first()?->id)
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->whereHas('roles', function ($q) use ($data) {
                                $q->where('id', $data['value']);
                            });
                        }
                    })
                    ->native(false),
            ], layout: FiltersLayout::Modal)
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