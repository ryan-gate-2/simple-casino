<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameslistResource\Pages;
use App\Filament\Resources\GameslistResource\RelationManagers;
use App\Models\Gameslist;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\View\View;

class GameslistResource extends Resource
{

    protected static ?string $navigationLabel = 'All Games'; 
    protected static ?string $slug = 'games-listing';
    protected static ?string $model = Gameslist::class;
    protected static ?string $navigationIcon = 'heroicon-o-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }
    protected function getHeader(): View
    {
        return view('filament.settings.custom-header');
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               ImageColumn::make('softswiss_s1'),
               Tables\Columns\TextColumn::make('game_name')->sortable(),
               TextColumn::make('game_desc')->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {


        return [
            'index' => Pages\ListGameslists::route('/'),
            'create' => Pages\CreateGameslist::route('/create'),
            'edit' => Pages\EditGameslist::route('/{record}/edit'),
        ];
    }    
}
