<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;

class CheckInTimeChart extends ChartWidget
{
    protected static ?string $heading = 'CheckIn Time Chart';

    protected function getData(): array
    {
        // Fetch attendance data for the current month
        $attendanceData = Attendance::whereMonth('check_in', now()->month)
            ->orderBy('check_in')
            ->get()
            // Group by date to get first check-in for each day
            ->groupBy(function ($attendance) {
                return Carbon::parse($attendance->check_in)->format('Y-m-d');
            })
            ->map(function ($dayAttendances) {
                // Get the first check-in for each day
                return $dayAttendances->first();
            });

        $labels = [];
        $checkInTimes = [];

        foreach ($attendanceData as $attendance) {
            $checkInTime = Carbon::parse($attendance->check_in);
            $labels[] = $checkInTime->format('d'); // Day of the month
            $checkInTimes[] = $checkInTime->timezone('Asia/Kolkata')->format('H.i'); // First check-in time
        }

        return [
            'datasets' => [
                [
                    'label' => 'CheckIn Time',
                    'data' => $checkInTimes,
                    'borderColor' => 'green',
                    'backgroundColor' => 'rgba(0, 128, 0, 0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}