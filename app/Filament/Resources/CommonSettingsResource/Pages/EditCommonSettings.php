<?php

namespace App\Filament\Resources\CommonSettingsResource\Pages;

use App\Filament\Resources\CommonSettingsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommonSettings extends EditRecord
{
    protected static string $resource = CommonSettingsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
