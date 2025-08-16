<?php

namespace App\Filament\Resources\CustomerServiceResource\Schemas;

use App\Models\CustomerService;
use App\Models\UserProfile;
use App\Services\UserService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;

class CustomerServiceForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Select::make('user_id')
                                    ->label('Pelanggan')
                                    ->required()
                                    ->options(function (?CustomerService $record) {
                                        return UserService::dropdownOptions($record?->user_id);
                                    })
                                    ->searchable()
                                    ->reactive()
                                    ->native(false)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('service_package_id', null);
                                    }),

                                Select::make('service_package_id')
                                    ->label('Paket Layanan')
                                    ->required()
                                    ->relationship('servicePackage', 'package_name', function (Builder $query, Get $get) {
                                        $accountType = UserProfile::userId($get('user_id'))
                                            ->first();

                                        $query->where('is_active', true);

                                        if ($accountType) {
                                            $query->where('plan_type', $accountType->account_type);
                                        }
                                    })
                                    ->preload()
                                    ->searchable()
                                    ->reactive()
                                    ->native(false),
                            ])
                    ])->columnSpan(['lg' => 2]),

                TextInput::make('price')
                    ->required()
                    ->numeric(),

                TextInput::make('payment_type')
                    ->required(),

                TextInput::make('username'),

                TextInput::make('password'),

                DatePicker::make('start_date'),

                DatePicker::make('end_date_time'),

                TextInput::make('status')
                    ->required(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?CustomerService $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?CustomerService $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
