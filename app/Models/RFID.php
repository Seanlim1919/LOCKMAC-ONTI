<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RFID extends Model
{
    use HasFactory;

    // The table associated with the model
    protected $table = 'rfids';

    // The attributes that are mass assignable
    protected $fillable = [
        'rfid_code',
    ];

    // The attributes that should be cast to native types
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Define the relationship to students
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'rfid_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
