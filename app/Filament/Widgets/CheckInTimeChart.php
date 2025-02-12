<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class CheckInTimeChart extends ChartWidget
{
    protected static ?string $heading = 'CheckIn Time Chart';

    protected function getData(): array
    {
        // Fetch attendance data for the current month using the correct column name "check_in"
        $attendanceData = Attendance::whereMonth('check_in', now()->month)
            ->orderBy('check_in')
            ->get();

        // Format data for chart
        $labels = [];
        $checkInTimes = [];

        foreach ($attendanceData as $attendance) {
            // Ensure we work with a Carbon instance (adjust if your model already casts this)
            $checkInTime = Carbon::parse($attendance->check_in);
            $labels[] = $checkInTime->format('d'); // Day of the month as label
            $checkInTimes[] = $checkInTime->format('H.i'); // Check-in time in 24-hr format (e.g. 09.15)
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
        return 'line'; // Line chart
    }

    
}
