<form action="{{ route('attendance.clockIn') }}" method="POST">
    @csrf
    <label>Employee Code:</label>
    <input type="text" name="employee_code" required>
    <button type="submit">Clock In</button>
</form>

<form action="{{ route('attendance.clockOut') }}" method="POST">
    @csrf
    <label>Employee ID:</label>
    <input type="text" name="employee_id" required>
    <button type="submit">Clock Out</button>
</form>
