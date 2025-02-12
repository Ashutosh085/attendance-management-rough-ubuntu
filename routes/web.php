<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;


Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockIn');
Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockOut');

Route::get('/', function () {
    return view('welcome');
});



Route::get('/clock', function () {
    return view('attendance');
});
