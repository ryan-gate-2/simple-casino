<?php

namespace App\Filament\Resources\GameslistResource\Pages;

use App\Filament\Resources\GameslistResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGameslist extends EditRecord
{
    protected static string $resource = GameslistResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
