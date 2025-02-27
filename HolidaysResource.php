<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HolidaysResource\Pages;
use App\Filament\Resources\HolidaysResource\RelationManagers;
use App\Models\Holidays;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;


class HolidaysResource extends Resource
{
    protected static ?string $model = Holidays::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup ="Attendance Management";

    public static function form(Form $form): Forms\Form
{
    return $form
        ->schema([
            TextInput::make('name')
                ->required()
                ->label('Holiday Name') // Ensures the name field is required
                ->maxLength(255),

            DatePicker::make('date')
                ->required()
                ->label('Date'),

            Select::make('type')
                ->options([
                    'public' => 'Public Holiday',
                    'optional' => 'Optional Holiday',
                    'religious' => 'Religious Holiday',
                ])
                ->default('public'),
        ]);
}
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    TextColumn::make('name')->sortable()->searchable()->label('Holiday Name'),
                    TextColumn::make('date')->sortable()->label('Date'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListHolidays::route('/'),
            'create' => Pages\CreateHolidays::route('/create'),
            'edit' => Pages\EditHolidays::route('/{record}/edit'),
        ];
    }
}
