<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class AttendanceSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_holiday_color',
        'religious_holiday_color',
        'optional_holiday_color',
        'weekend_color',
        'present_full_color',
        'present_partial_color',
        'present_minimal_color',
        'absent_color',
        'pending_color',
        'day_off_settings',
    ];

    protected $casts = [
        'day_off_settings' => 'array',
    ];

    /**
     * Get default settings if none exist
     */
    public static function getSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'public_holiday_color' => 'rgb(255, 105, 180)',
                'religious_holiday_color' => 'rgb(82, 243, 243)',
                'optional_holiday_color' => 'rgb(155, 38, 223)',
                'weekend_color' => 'rgb(88, 88, 88)',
                'present_full_color' => 'rgb(54, 190, 54)',
                'present_partial_color' => 'rgb(0, 122, 255)',
                'present_minimal_color' => 'rgb(255, 149, 0)',
                'absent_color' => 'rgb(235, 33, 33)',
                'pending_color' => 'rgb(223, 223, 223)',
                'day_off_settings' => [
                    'monday' => [],
                    'tuesday' => [],
                    'wednesday' => [],
                    'thursday' => [],
                    'friday' => [],
                    'saturday' => ['all'],
                    'sunday' => ['all'],
                ],
            ]);
        }
        
        return $settings;
    }
    
    /**
     * Check if a specific date is a configured day off
     */
    public static function isDayOff($date)
    {
        $settings = self::getSettings();
        $dayOfWeek = strtolower($date->format('l'));
        $weekOfMonth = ceil($date->format('j') / 7);
        
        // Check if this day of week is always off
        if (in_array('all', $settings->day_off_settings[$dayOfWeek] ?? [])) {
            return true;
        }
        
        // Check if this specific occurrence of the day is off
        return in_array($weekOfMonth, $settings->day_off_settings[$dayOfWeek] ?? []);
    }
    
    /**
     * Get the color for a specific holiday type
     */
    public function getHolidayColor($type)
    {
        switch ($type) {
            case 'public':
                return $this->public_holiday_color;
            case 'religious':
                return $this->religious_holiday_color;
            case 'optional':
                return $this->optional_holiday_color;
            default:
                return $this->public_holiday_color;
        }
    }
}