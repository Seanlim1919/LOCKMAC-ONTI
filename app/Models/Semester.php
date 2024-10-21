<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $table = 'semester';
    protected $fillable = ['semester_name', 'start_year', 'end_year'];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}

