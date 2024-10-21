<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'email', 'password', 
        'phone_number', 'gender', 'date_of_birth', 'role', 'rfid_id', 'google_id', 'user_image','status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

     // User.php (Model)
     public function schedule()
     {
         return $this->hasOne(Schedule::class, 'faculty_id');
     }

     public function schedules()
    {
        return $this->hasMany(Schedule::class, 'faculty_id');
    }

    public function rfid()
    {
        return $this->hasOne(RFID::class, 'id', 'rfid_id');
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, Course::class);
    }
    
    public function studentAttendances()
    {
        return $this->hasMany(StudentAttendance::class, 'faculty_id');
    }
     
    
}
