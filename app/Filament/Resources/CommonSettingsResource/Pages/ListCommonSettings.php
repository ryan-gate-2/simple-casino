<?php

namespace App\Filament\Resources\CommonSettingsResource\Pages;

use App\Filament\Resources\CommonSettingsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommonSettings extends ListRecords
{
    protected static string $resource = CommonSettingsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
