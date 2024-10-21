<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty_id',
        'course_id',
        'course_code',
        'course_name',
        'day',
        'start_time',
        'end_time',
        'program',
        'year',
        'section',
        'semester_id', 
        'status'
    ];

    public function faculty()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    
    public function attendance()
    {
        return $this->hasMany(StudentAttendance::class, 'faculty_id', 'faculty_id');
    }
}
