<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Attendance;
use Filament\Notifications\Notification;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Actions\Action;

class AttendanceWidget extends Widget
{
    protected static string $view = 'filament.widgets.attendance-widget';

    protected int|string|array $columnSpan = 1;

    public $isClockedIn = false;
    public $clockInTime = null;
    public $clockOutTime = null;
    public $device = 'Unknown';
    public $averageArrivalTime = null;
    public $employee = null;
    public $showConfirmation = false; // Control modal visibility

    protected $listeners = ['toggleClockConfirmed' => 'toggleClock'];

    public function mount()
    {
        $this->device = request()->header('User-Agent') ?? 'Unknown';
        $this->loadEmployee();
        $this->loadAttendanceData();
    }

    /**
     * Manually Load Employee Data
     */
    public function loadEmployee()
    {
        // Replace this with your logic for retrieving the current employee
        $this->employee = Employee::first(); // Example: Get the first employee
    }

    public function loadAttendanceData()
    {
        if (!$this->employee) {
            $this->isClockedIn = false;
            return;
        }

        $attendance = Attendance::where('employee_id', $this->employee->id)->latest()->first();

        $this->isClockedIn = $attendance && !$attendance->check_out;
        $this->clockInTime = $attendance && $attendance->check_in ? Carbon::parse($attendance->check_in)->timezone('Asia/Kolkata')->format('M d, h:i A') : 'N/A';
        $this->clockOutTime = $attendance && $attendance->check_out ? Carbon::parse($attendance->check_out)->timezone('Asia/Kolkata')->format('M d, h:i A') : 'N/A';

        $this->averageArrivalTime = $this->calculateAverageArrival($this->employee->id);
    }

    /**
     * Show confirmation before proceeding
     */
    public function confirmClockAction()
    {
        // Show confirmation notification with actions
        Notification::make()
            ->title($this->isClockedIn ? 'Confirm Clock Out' : 'Confirm Clock In')
            ->body('Are you sure you want to ' . ($this->isClockedIn ? 'clock out' : 'clock in') . ' now?')
            ->actions([
                Action::make('confirm')
                    ->label('Yes, ' . ($this->isClockedIn ? 'Clock Out' : 'Clock In'))
                    ->color($this->isClockedIn ? 'danger' : 'success')
                    ->button()
                    ->close()
                    ->dispatch('toggleClockConfirmed', []),

                Action::make('cancel')
                    ->label('Cancel')
                    ->close(),
            ])
            ->persistent()
            ->send();
    }

    // Alternatively, set showConfirmation flag to true to show the modal in the blade file
    public function showConfirmationModal()
    {
        $this->showConfirmation = true;
    }

    public function cancelClockAction()
    {
        $this->showConfirmation = false;
    }

    public function toggleClock()
    {
        if (!$this->employee) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('Employee information not found.')
                ->send();
            return;
        }

        $latestAttendance = Attendance::where('employee_id', $this->employee->id)->latest()->first();

        if ($this->isClockedIn) {
            // Clock Out
            if ($latestAttendance && !$latestAttendance->check_out) {
                $latestAttendance->update(['check_out' => now()]);
                $this->clockOutTime = now()->timezone('Asia/Kolkata')->format('M d, h:i A');
                $this->isClockedIn = false;

                Notification::make()
                    ->title('Clock Out Successful')
                    ->success()
                    ->body('You have successfully clocked out.')
                    ->send();
            }
        } else {
            // Clock In
            Attendance::create([
                'employee_id' => $this->employee->id,
                'check_in' => now(),
                'device_id' => $this->device,
            ]);

            $this->clockInTime = now()->timezone('Asia/Kolkata')->format('M d, h:i A');
            $this->isClockedIn = true;

            Notification::make()
                ->title('Clock In Successful')
                ->success()
                ->body('You have successfully clocked in.')
                ->send();
        }

        $this->showConfirmation = false;
        $this->loadAttendanceData();
    }



    protected function calculateAverageArrival($employeeId)
    {
        $checkInTimes = Attendance::where('employee_id', $employeeId)
            ->whereMonth('check_in', Carbon::now()->month)
            ->pluck('check_in')
            ->filter();

        if ($checkInTimes->isEmpty()) {
            return 'N/A';
        }

        // First convert all times to Asia/Kolkata timezone, then extract just the time portion
        $totalSeconds = 0;
        $count = 0;

        foreach ($checkInTimes as $time) {
            $carbonTime = Carbon::parse($time)->timezone('Asia/Kolkata');
            // Get seconds since midnight for just the time part
            $seconds = $carbonTime->hour * 3600 + $carbonTime->minute * 60 + $carbonTime->second;
            $totalSeconds += $seconds;
            $count++;
        }

        $averageSeconds = $totalSeconds / $count;

        // Create a new Carbon instance at midnight today in Asia/Kolkata timezone
        $averageTime = Carbon::today('Asia/Kolkata')->addSeconds($averageSeconds);

        return $averageTime->format('h:i A');
    }
}