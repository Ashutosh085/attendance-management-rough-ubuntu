<div class="p-4 bg-white rounded-lg shadow mb-4">
    <h2 class="text-xl font-semibold">CheckIn Time Chart</h2>
    <p>Attendance Month is <strong>{{ session('attendance_month') }}</strong></p>
    <p>Employee is <strong>{{ session('employee_code') }}: {{ session('employee_name') }}</strong></p>
    <p>Average Time of Arrival is <strong>{{ session('average_arrival') }}</strong></p>
</div>

<!-- Render the Filament widget -->
{{ \Filament\Facades\Filament::render('check-in-time-chart') }}
