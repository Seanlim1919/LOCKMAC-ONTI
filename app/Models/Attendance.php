<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'entered_at',
        'exited_at',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function faculty()
    {
        return $this->user()->where('role', 'faculty');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }



}
