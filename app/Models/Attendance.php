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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
