<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {
        $employee = Employee::where('employee_code', $request->employee_code)->first();

        if (!$employee) {
            return back()->with('error', 'Invalid Employee Code');
        }

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'check_in' => now(),
        ]);

        return back()->with('success', 'Clocked In Successfully!');
    }

    public function clockOut(Request $request)
    {
        $attendance = Attendance::where('employee_id', $request->employee_id)
            ->whereNull('check_out')
            ->latest()
            ->first();

        if (!$attendance) {
            return back()->with('error', 'No active check-in found');
        }

        $attendance->update([
            'check_out' => now(),
            'work_hours' => Carbon::parse($attendance->check_in)->diffInHours(now()),
        ]);

        return back()->with('success', 'Clocked Out Successfully!');
    }
}
