<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Attendance;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use App\Models\Employee;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;

use Illuminate\Support\Facades\DB; // Use DB facade for fetching data

class AttendanceWidget extends Widget
{
    protected static string $view = 'filament.widgets.attendance-widget';

    protected int | string | array $columnSpan = 1;

    public $isClockedIn = false;
    public $clockInTime = null;
    public $clockOutTime = null;
    public $device = 'Unknown';
    public $averageArrivalTime = null;
    // public $requestReason = '';
    public $employee = null; 

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
        $this->clockInTime = $attendance ? Carbon::parse($attendance->check_in)->timezone('Asia/Kolkata')->format('M d, h:i A') : 'N/A';
        $this->clockOutTime = $attendance ? Carbon::parse($attendance->check_out)->timezone('Asia/Kolkata')->format('M d, h:i A') : 'N/A';

        $this->averageArrivalTime = $this->calculateAverageArrival($this->employee->id);
    }

    public function getActions(): array
    {
        return [
            Action::make('toggle_clock')
                ->label($this->isClockedIn ? 'Clock Out' : 'Clock In')
                ->color($this->isClockedIn ? 'danger' : 'success')
                ->icon($this->isClockedIn ? 'heroicon-o-logout' : 'heroicon-o-clock')
                ->action(fn () => $this->toggleClock())
                ->requiresConfirmation()
                ->modalHeading($this->isClockedIn ? 'Confirm Clock Out' : 'Confirm Clock In')
                ->modalDescription($this->isClockedIn ? 'Are you sure you want to clock out?' : 'Are you sure you want to clock in?')
                ->modalSubmitActionLabel('Yes, Proceed'),
        ];
    }

   

    public function toggleClock()
    {
        
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

        $this->loadAttendanceData();
    }

    public function requestPermission($reason)
    {
        if (!$this->employee) {
            return;
        }

        // Log the request (or save to database)
        // \Log::info("Permission request from Employee ID {$this->employee->id}: {$reason}");

        // Notification::make()
        //     ->title('Request Submitted')
        //     ->success()
        //     ->body('Your request has been sent to the admin.')
        //     ->send();
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

        $averageTimestamp = $checkInTimes
            ->map(fn($time) => Carbon::parse($time)->timestamp)
            ->average();

        return Carbon::createFromTimestamp($averageTimestamp)->timezone('Asia/Kolkata')->format('h:i A');
    }

}
