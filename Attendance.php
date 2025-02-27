<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Carbon\Carbon;
use Illuminate\Support\Carbon;



class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['name','employee_id', 'check_in','check_out','work_hours'];

    public function getWorkHoursAttribute()
    {
        if ($this->check_in && $this->check_out) {
            return Carbon::parse($this->check_in)->diffInHours(Carbon::parse($this->check_out));
        }
        return 0;
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    


    
}

