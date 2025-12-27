<?php

namespace App\Filament\Resources\CustomerServiceResource\RelationManagers;

use App\Enums\BillingType;
use App\Helpers\IdrCurrency;
use App\Models\ExtraCost;
use App\Services\ExtraCostService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AdditionalServiceFeesRelationManager extends RelationManager
{
    protected static string $relationship = 'additionalServiceFees';

    protected static ?string $title = 'Biaya Tambahan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Select::make('extra_cost_id')
                            ->label('Biaya Tambahan')
                            ->options(collect(ExtraCostService::options())->map(fn($item) => $item['name'] . '(' . IdrCurrency::convert($item['fee']) . ')'))
                            ->required()
                            ->native(false),

                        Forms\Components\Radio::make('is_active')
                            ->label('Aktifkan')
                            ->boolean()
                            ->required()
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('extraCost.name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('fee')
                    ->label('Biaya')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('extraCost.billing_type')
                    ->label('Jenis Tagihan')
                    ->formatStateUsing(fn($state): string => BillingType::tryFrom($state)?->getLabel() ?? $state),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Baru')
                    ->modalHeading('Tambah Biaya Tambahan')
                    ->modalWidth('md')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['fee'] = ExtraCost::find($data['extra_cost_id'])?->fee;

                        return $data;
                    })
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->modalHeading('Ubah Biaya Tambahan')
                        ->modalWidth('md'),

                    Tables\Actions\DeleteAction::make()
                        ->modalHeading('Hapus Biaya Tambahan')
                ])
            ])
            ->bulkActions([
                //
            ]);
    }
}
