<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextInput;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;




class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    protected static ?string $navigationGroup ="Attendance Management";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_id')
                    ->required()
                    // ->relationship(name: 'Employee', titleattribute:'name')
                    ->numeric(),
                    Forms\Components\DateTimePicker::make('check_in')->timezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                    Forms\Components\DateTimePicker::make('check_out')->timezone('Asia/Kolkata')->format('Y-m-d H:i:s'),
                    
                Forms\Components\TextInput::make('work_hours')
                    ->numeric(),
                // Forms\Components\Hidden::make('time_ust')->default(now()->setTimezone('UTC')),
                // Forms\Components\Hidden::make('time_ist')->default(now()->setTimezone('Asia/Kolkata')),
              //  Forms\Components\TextInput::make('created_at'),
               // Forms\Components\TextInput::make('updated_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Employee Id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_in')
                    ->dateTime()
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->timezone('Asia/Kolkata')->format('d-m-Y H:i:s'))
                    ->label("Check In")
                    ->sortable('desc'),
                Tables\Columns\TextColumn::make('check_out')
                    ->dateTime()
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->timezone('Asia/Kolkata')->format('d-m-Y H:i:s'))
                    ->label("Check Out")
                    ->sortable(),
                Tables\Columns\TextColumn::make('work_hours')
                    ->numeric()
                    ->label("Work Hours")
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
