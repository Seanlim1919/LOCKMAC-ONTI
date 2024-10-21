<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['course_name', 'course_code','sem_avail', 'program', 'year_avail'];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    
    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_student', 'course_id', 'student_id');
    }
    public function studentAttendances()
{
    return $this->hasMany(StudentAttendance::class);
}
public function schedules()
{
    return $this->hasMany(Schedule::class, 'course_id');
}
}
