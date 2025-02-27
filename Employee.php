<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;  

    protected $fillable = ['name','employee_code'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    

    public function attendances(){
        return $this->hasMany(Attendance::class);
    }
}
