<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceSettingsResource\Pages;

use App\Models\AttendanceSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceSettingsResource extends Resource
{
    protected static ?string $model = AttendanceSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    
    protected static ?string $navigationGroup = 'Attendance Management';
    
    protected static ?string $navigationLabel = 'Attendance Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Holiday & Attendance Colors')
                    ->schema([
                        Forms\Components\ColorPicker::make('public_holiday_color')
                            ->label('Public Holiday Color')
                            ->required(),
                        Forms\Components\ColorPicker::make('religious_holiday_color')
                            ->label('Religious Holiday Color')
                            ->required(),
                        Forms\Components\ColorPicker::make('optional_holiday_color')
                            ->label('Optional Holiday Color')
                            ->required(),
                        Forms\Components\ColorPicker::make('weekend_color')
                            ->label('Weekend Color')
                            ->required(),
                        Forms\Components\ColorPicker::make('present_full_color')
                            ->label('Present (≥ 8 hours) Color')
                            ->required(),
                        Forms\Components\ColorPicker::make('present_partial_color')
                            ->label('Present (5-8 hours) Color')
                            ->required(),
                        Forms\Components\ColorPicker::make('present_minimal_color')
                            ->label('Present (< 5 hours) Color')
                            ->required(),
                        Forms\Components\ColorPicker::make('absent_color')
                            ->label('Absent Color')
                            ->required(),
                        Forms\Components\ColorPicker::make('pending_color')
                            ->label('Pending Color')
                            ->required(),
                    ])->columns(3),
                
                Forms\Components\Section::make('Days Off Configuration')
                    ->schema([
                        Forms\Components\Repeater::make('monday_config')
                            ->label('Monday Days Off')
                            ->schema([
                                Forms\Components\Select::make('week')
                                    ->label('Week of Month')
                                    ->options([
                                        'all' => 'Every Monday',
                                        '1' => '1st Monday',
                                        '2' => '2nd Monday',
                                        '3' => '3rd Monday',
                                        '4' => '4th Monday',
                                        '5' => '5th Monday'
                                    ])
                                    ->required(),
                            ])
                            ->columnSpanFull()
                            ->itemLabel(fn (array $state): ?string => $state['week'] === 'all' ? 'Every Monday' : 'The ' . $state['week'] . match($state['week']) {
                                '1' => 'st',
                                '2' => 'nd',
                                '3' => 'rd',
                                default => 'th',
                            } . ' Monday'),
                            
                        Forms\Components\Repeater::make('tuesday_config')
                            ->label('Tuesday Days Off')
                            ->schema([
                                Forms\Components\Select::make('week')
                                    ->label('Week of Month')
                                    ->options([
                                        'all' => 'Every Tuesday',
                                        '1' => '1st Tuesday',
                                        '2' => '2nd Tuesday',
                                        '3' => '3rd Tuesday',
                                        '4' => '4th Tuesday',
                                        '5' => '5th Tuesday'
                                    ])
                                    ->required(),
                            ])
                            ->columnSpanFull()
                            ->itemLabel(fn (array $state): ?string => $state['week'] === 'all' ? 'Every Tuesday' : 'The ' . $state['week'] . match($state['week']) {
                                '1' => 'st',
                                '2' => 'nd',
                                '3' => 'rd',
                                default => 'th',
                            } . ' Tuesday'),
                            
                        Forms\Components\Repeater::make('wednesday_config')
                            ->label('Wednesday Days Off')
                            ->schema([
                                Forms\Components\Select::make('week')
                                    ->label('Week of Month')
                                    ->options([
                                        'all' => 'Every Wednesday',
                                        '1' => '1st Wednesday',
                                        '2' => '2nd Wednesday',
                                        '3' => '3rd Wednesday',
                                        '4' => '4th Wednesday',
                                        '5' => '5th Wednesday'
                                    ])
                                    ->required(),
                            ])
                            ->columnSpanFull()
                            ->itemLabel(fn (array $state): ?string => $state['week'] === 'all' ? 'Every Wednesday' : 'The ' . $state['week'] . match($state['week']) {
                                '1' => 'st',
                                '2' => 'nd',
                                '3' => 'rd',
                                default => 'th',
                            } . ' Wednesday'),
                            
                        Forms\Components\Repeater::make('thursday_config')
                            ->label('Thursday Days Off')
                            ->schema([
                                Forms\Components\Select::make('week')
                                    ->label('Week of Month')
                                    ->options([
                                        'all' => 'Every Thursday',
                                        '1' => '1st Thursday',
                                        '2' => '2nd Thursday',
                                        '3' => '3rd Thursday',
                                        '4' => '4th Thursday',
                                        '5' => '5th Thursday'
                                    ])
                                    ->required(),
                            ])
                            ->columnSpanFull()
                            ->itemLabel(fn (array $state): ?string => $state['week'] === 'all' ? 'Every Thursday' : 'The ' . $state['week'] . match($state['week']) {
                                '1' => 'st',
                                '2' => 'nd',
                                '3' => 'rd',
                                default => 'th',
                            } . ' Thursday'),
                            
                        Forms\Components\Repeater::make('friday_config')
                            ->label('Friday Days Off')
                            ->schema([
                                Forms\Components\Select::make('week')
                                    ->label('Week of Month')
                                    ->options([
                                        'all' => 'Every Friday',
                                        '1' => '1st Friday',
                                        '2' => '2nd Friday',
                                        '3' => '3rd Friday',
                                        '4' => '4th Friday',
                                        '5' => '5th Friday'
                                    ])
                                    ->required(),
                            ])
                            ->columnSpanFull()
                            ->itemLabel(fn (array $state): ?string => $state['week'] === 'all' ? 'Every Friday' : 'The ' . $state['week'] . match($state['week']) {
                                '1' => 'st',
                                '2' => 'nd',
                                '3' => 'rd',
                                default => 'th',
                            } . ' Friday'),
                            
                        Forms\Components\Repeater::make('saturday_config')
                            ->label('Saturday Days Off')
                            ->schema([
                                Forms\Components\Select::make('week')
                                    ->label('Week of Month')
                                    ->options([
                                        'all' => 'Every Saturday',
                                        '1' => '1st Saturday',
                                        '2' => '2nd Saturday',
                                        '3' => '3rd Saturday',
                                        '4' => '4th Saturday',
                                        '5' => '5th Saturday'
                                    ])
                                    ->required(),
                            ])
                            ->columnSpanFull()
                            ->itemLabel(fn (array $state): ?string => $state['week'] === 'all' ? 'Every Saturday' : 'The ' . $state['week'] . match($state['week']) {
                                '1' => 'st',
                                '2' => 'nd',
                                '3' => 'rd',
                                default => 'th',
                            } . ' Saturday')
                            ->default([['week' => 'all']]),
                            
                        Forms\Components\Repeater::make('sunday_config')
                            ->label('Sunday Days Off')
                            ->schema([
                                Forms\Components\Select::make('week')
                                    ->label('Week of Month')
                                    ->options([
                                        'all' => 'Every Sunday',
                                        '1' => '1st Sunday',
                                        '2' => '2nd Sunday',
                                        '3' => '3rd Sunday',
                                        '4' => '4th Sunday',
                                        '5' => '5th Sunday'
                                    ])
                                    ->required(),
                            ])
                            ->columnSpanFull()
                            ->itemLabel(fn (array $state): ?string => $state['week'] === 'all' ? 'Every Sunday' : 'The ' . $state['week'] . match($state['week']) {
                                '1' => 'st',
                                '2' => 'nd',
                                '3' => 'rd',
                                default => 'th',
                            } . ' Sunday')
                            ->default([['week' => 'all']]),
                    ]),
            ])
            ->statePath('data');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceSettings::route('/'),
        ];
    }
    
    public static function getModelLabel(): string
    {
        return 'Attendance Settings';
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ? 'Configured' : 'Not Configured';
    }
    
    public static function canCreate(): bool
    {
        return static::getModel()::count() === 0;
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->limit(1);
    }
    
    public static function beforeSave($record, $data): void
    {
        // Convert repeater data to the day_off_settings format
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $dayOffSettings = [];
        
        foreach ($days as $day) {
            $configKey = "{$day}_config";
            $dayOffSettings[$day] = collect($data[$configKey] ?? [])
                ->pluck('week')
                ->toArray();
        }
        
        $record->day_off_settings = $dayOffSettings;
    }
    
    public static function afterFill($record, $data): void
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $formData = [];
        
        foreach ($days as $day) {
            $configKey = "{$day}_config";
            $formData[$configKey] = collect($record->day_off_settings[$day] ?? [])
                ->map(fn ($week) => ['week' => $week])
                ->toArray();
        }
        
        $record->data = array_merge($record->data ?? [], $formData);
    }
}