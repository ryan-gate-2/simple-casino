<?php

namespace App\Filament\Resources\GameslistResource\Pages;

use App\Filament\Resources\GameslistResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGameslists extends ListRecords
{
    protected static string $resource = GameslistResource::class;
    protected static ?string $title = 'Complete Games Listing';

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
