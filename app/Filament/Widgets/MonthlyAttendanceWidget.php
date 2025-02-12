<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;

class MonthlyAttendanceWidget extends Widget
{
    protected static string $view = 'filament.widgets.monthly-attendance-widget';
    
    protected int | string | array $columnSpan = 2;

    public function getViewData(): array
    {
        $user = Auth::user();
        $employee = $user->employee;  // Ensure relation exists

        if (!$employee) {
            return [
                'averageArrivalTime' => '09:26 A.M',
                'workHours' => [],
                'days' => [],
            ];
        }

        // Get attendances for the current month
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();

        // Process data for display
        $workHours = [];
        $days = [];

        foreach ($attendances as $attendance) {
            $day = $attendance->created_at->format('d'); // Get day number
            $workHours[$day] = $attendance->work_hours ?? 0; // Store work hours
            $days[] = $day;
        }

        return [
            'averageArrivalTime' => $attendances->avg('arrival_time') ? round($attendances->avg('arrival_time'), 2) . ' AM' : 'N/A',
            'workHours' => $workHours,
            'days' => $days,
        ];
    }
}
