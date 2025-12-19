<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource\UserResource;
use Cheesegrits\FilamentGoogleMaps\Concerns\InteractsWithMaps;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use InteractsWithMaps;

    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Ubah Data';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->modalHeading('Hapus pelanggan'),
            ForceDeleteAction::make()
                ->modalHeading('Hapus selamanya'),
            RestoreAction::make()
                ->modalHeading('Pulihkan data'),
        ];
    }
}
