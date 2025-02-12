<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Attendance;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use App\Models\Employee;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB; // Use DB facade for fetching data

class AttendanceWidget extends Widget
{
    protected static string $view = 'filament.widgets.attendance-widget';

    protected int | string | array $columnSpan = 2;

    public $isClockedIn = false;
    public $clockInTime = null;
    public $clockOutTime = null;
    public $device = 'Unknown';
    public $averageArrivalTime = null;
    public $requestReason = '';
    public $employee = null; // Manually assigned employee

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
        $this->clockInTime = $attendance ? Carbon::parse($attendance->check_in)->format('M d, h:i A') : 'N/A';
        $this->clockOutTime = $attendance ? Carbon::parse($attendance->check_out)->format('M d, h:i A') : 'N/A';

        $this->averageArrivalTime = $this->calculateAverageArrival($this->employee->id);
    }

    public function getActions(): array
    {
        return [
            Action::make('toggle_clock')
            ->label($this->isClockedIn ? 'Clock Out' : 'Clock In')
            ->color($this->isClockedIn ? 'danger' : 'success')
            ->icon($this->isClockedIn ? 'heroicon-o-logout' : 'heroicon-o-clock')
            ->visible(fn () => optional($this->employee)->can_clock_in || optional($this->employee)->can_clock_out)
            ->form([])
            ->action(function () {
                $this->toggleClock();
            })
            ->requiresConfirmation()
            ->modalButton('Yes, Proceed')
            ->modalHeading($this->isClockedIn ? 'Confirm Clock Out' : 'Confirm Clock In')
            ->modalDescription($this->isClockedIn ? 'Are you sure you want to clock out?' : 'Are you sure you want to clock in?'),

            // Action::make('request_permission')
            //     ->label('Request Permission')
            //     ->color('warning')
            //     ->icon('heroicon-o-exclamation-circle')
            //     ->modalHeading('Request Permission')
            //     ->modalDescription('You do not have permission to clock in or out. Please request access.')
            //     ->form([
            //         Textarea::make('requestReason')
            //             ->label('Reason for Request')
            //             ->required()
            //             ->placeholder('Explain why you need permission...')
            //             ->columnSpanFull(),
            //     ])
            //     ->modalSubmitActionLabel('Submit Request')
            //     ->action(fn (array $data) => $this->requestPermission($data['requestReason']))
            //     ->visible(fn () => !$this->employee || (!$this->employee->can_clock_in && !$this->employee->can_clock_out)),
        ];
    }

    public function toggleClock()
    {
        if (!$this->employee) {
            return;
        }

        if ($this->isClockedIn && !$this->employee->can_clock_out) {
            Notification::make()
                ->title('Permission Denied')
                ->danger()
                ->body('You do not have permission to clock out.')
                ->send();
            return;
        }

        if (!$this->isClockedIn && !$this->employee->can_clock_in) {
            Notification::make()
                ->title('Permission Denied')
                ->danger()
                ->body('You do not have permission to clock in.')
                ->send();
            return;
        }

        $latestAttendance = Attendance::where('employee_id', $this->employee->id)->latest()->first();

        if ($this->isClockedIn) {
            // Clock Out
            if ($latestAttendance && !$latestAttendance->check_out) {
                $latestAttendance->update(['check_out' => now()]);
                $this->clockOutTime = now()->format('M d, h:i A');
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

            $this->clockInTime = now()->format('M d, h:i A');
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
        \Log::info("Permission request from Employee ID {$this->employee->id}: {$reason}");

        Notification::make()
            ->title('Request Submitted')
            ->success()
            ->body('Your request has been sent to the admin.')
            ->send();
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

        return Carbon::createFromTimestamp($averageTimestamp)->format('h:i A');
    }
}
