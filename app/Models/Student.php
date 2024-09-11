<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students'; 

    protected $fillable = [
        'student_number', 'first_name', 'middle_name', 'last_name', 'program', 'year', 'section', 'gender', 'pc_number', 'rfid_id'
    ];

    public function getFirstnameAttribute()
    {
        return $this->attributes['first_name'];
    }

    public function setFirstnameAttribute($value)
    {
        $this->attributes['first_name'] = $value;
    }

    public function getMiddlenameAttribute()
    {
        return $this->attributes['middle_name'];
    }

    public function setMiddlenameAttribute($value)
    {
        $this->attributes['middle_name'] = $value;
    }

    public function getLastnameAttribute()
    {
        return $this->attributes['last_name'];
    }

    public function setLastnameAttribute($value)
    {
        $this->attributes['last_name'] = $value;
    }

    public function rfid()
    {
    return $this->belongsTo(RFID::class);
    }

}
