<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Carbon\Carbon;
use Illuminate\Support\Carbon;



class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'check_in','check_out','work hours', 'time_ust','time_ist'];

    public function getWorkHoursAttribute()
    {
        if ($this->check_in && $this->check_out) {
            return Carbon::parse($this->check_in)->diffInHours(Carbon::parse($this->check_out));
        }
        return 0;
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    


    // For ust and ist 
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($attendance) {
    //         $currentTime = Carbon::now('UTC'); // Get current time in UST (UTC)

    //         $attendance->time_ust = $currentTime; // Store UTC time
    //         $attendance->time_ist = $currentTime->copy()->setTimezone('Asia/Kolkata'); // Convert to IST
    //     });
    // }




//     public function attendances()
// {
//     return $this->hasMany(Attendance::class);
// }
}

