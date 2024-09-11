<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RFID extends Model
{
    use HasFactory;

    protected $table = 'rfids';

    protected $fillable = [
        'rfid_code',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'rfid_id');
    }

}
