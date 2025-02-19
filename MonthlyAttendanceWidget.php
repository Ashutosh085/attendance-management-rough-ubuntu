<?php

namespace App\Filament\Widgets;

use App\Models\Holidays;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class MonthlyAttendanceWidget extends Widget
{
    protected static string $view = 'filament.widgets.monthly-attendance-widget';
    protected int|string|array $columnSpan = 'full';

    public $month;
    public $year;
    public $selectedMonth;
    public $selectedYear;

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
        $this->selectedMonth = $this->month;
        $this->selectedYear = $this->year;
    }

    public function applyFilter()
    {
        // Update month and year based on selection
        $this->month = $this->selectedMonth;
        $this->year = $this->selectedYear;

        // Refresh calendar data
        $this->generateCalendar();
    }

    protected function getViewData(): array
{
    $user = Auth::user();
    $employee = $user->employee;

    if (!$employee) {
        return [
            'calendar' => collect(),  // ✅ Fix: Ensure empty collection is passed
            'employeeName' => 'N/A',
            'employeeCode' => 'N/A',
            'selectedMonth' => $this->selectedMonth,
            'selectedYear' => $this->selectedYear,
            'averageArrivalTime' => 'N/A',
            'averageWorkHours' => 0,
        ];
    }

    $startOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1);
    $endOfMonth = $startOfMonth->copy()->endOfMonth();
    $holidays = Holidays::whereBetween('date', [$startOfMonth, $endOfMonth])->get();

    // Fetch attendance records and group them by date
    $attendances = Attendance::where('employee_id', $employee->id)
        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->get()
        ->groupBy(fn($attendance) => $attendance->created_at->timezone('Asia/Kolkata')->format('Y-m-d'));

    $calendar = collect();
    $currentDate = $startOfMonth->copy();

    while ($currentDate <= $endOfMonth) {
        $date = $currentDate->timezone('Asia/Kolkata')->format('Y-m-d');
        $dayAttendances = $attendances->get($date, collect());
        $holiday = $holidays->firstWhere('date', $date);

        $calendar->push([
            'date' => $currentDate->copy(),
            'isWeekend' => $currentDate->isSaturday() || $currentDate->isSunday(),
            'isHoliday' => $holiday ? true : false,
            'holidayName' => $holiday ? $holiday->name : null,
            'status' => $this->calculateDayStatus($dayAttendances, $currentDate, $holiday),
            'totalWorkHours' => $dayAttendances->sum('work_hours'),
            'arrivalTime' => $this->getArrivalTime($dayAttendances),
        ]);

        $currentDate->addDay();
    }

    return [
        'calendar' => $calendar,  // ✅ Fix: Ensure $calendar is passed to Blade
        'employeeName' => $employee->name ?? 'N/A',
        'employeeCode' => $employee->employee_code ?? 'N/A',
        'selectedMonth' => $this->selectedMonth,
        'selectedYear' => $this->selectedYear,
        'averageArrivalTime' => $this->calculateAverageArrivalTime($attendances),
        'averageWorkHours' => $this->calculateAverageWorkHours($attendances),
    ];
}


    private function calculateDayStatus($attendances, $date, $holiday = null)
    {
        if ($holiday) {
            return 'holiday';
        }
        if ($date->isSaturday() || $date->isSunday()) {
            return 'weekend';
        }
        if ($date->isFuture()) {
            return 'pending';
        }
        return $attendances->isEmpty() ? 'absent' : 'present';
    }

    private function getArrivalTime($attendances)
    {
        $checkIns = $attendances->pluck('check_in')->filter();
        return $checkIns->isEmpty() ? 'N/A' : Carbon::parse($checkIns->min())->format('h:i A');
    }

    private function calculateAverageArrivalTime($attendances)
    {
        $checkIns = $attendances->flatten()
            ->filter(fn($attendance) => $attendance->check_in)
            ->map(fn($attendance) => Carbon::parse($attendance->check_in));

        return $checkIns->isEmpty() ? 'N/A' : Carbon::createFromTimestamp(
            $checkIns->average(fn($time) => $time->secondsSinceMidnight())
        )->timezone('Asia/Kolkata')->format('h:i A');
    }

    private function calculateAverageWorkHours($attendances)
    {
        return $attendances->flatten()->avg('work_hours') ?? 0;
    }
}
