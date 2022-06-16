<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommonSettingsResource\Pages;
use App\Filament\Resources\CommonSettingsResource\RelationManagers;
use App\Models\CommonSettings;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommonSettingsResource extends Resource
{
    protected static ?string $model = CommonSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-terminal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('setting_key')
                            ->required()
                            ->label('Setting\'s Key'),
                        Forms\Components\TextInput::make('setting_value')
                            ->required()
                            ->label('Setting\'s Value'),
                        Forms\Components\MarkdownEditor::make('setting_desc')
                            ->label('Internal Description'),
                    ])
                    ->columnSpan([
                        'sm' => 2,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               Tables\Columns\TextColumn::make('setting_key')->sortable(),
               Tables\Columns\TextColumn::make('setting_value')->limit(10)->sortable(),
               Tables\Columns\TextColumn::make('setting_desc')->limit(50),

            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
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
            'index' => Pages\ListCommonSettings::route('/'),
            'create' => Pages\CreateCommonSettings::route('/create'),
            'edit' => Pages\EditCommonSettings::route('/{record}/edit'),
        ];
    }    
}
