<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_number',
        'first_name',
        'middle_name',
        'last_name',
        'program',
        'year_and_section',
        'gender',
        'pc_number',
        'rfid',
    ];

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function rfid()
    {
        return $this->belongsTo(RFID::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student', 'student_id', 'course_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function faculty()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

}
