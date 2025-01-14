<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',  'faculty_id', 'entered_at', 'exited_at'
    ];
    
    protected $casts = [
        'entered_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function faculty()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

}
