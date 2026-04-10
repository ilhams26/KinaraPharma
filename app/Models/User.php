<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'no_hp',
        'password',
        'role',
        'tanggal_lahir',
        'jenis_kelamin',
        'is_dark_mode',
        'otp',
        'otp_verified_at',
        'otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'otp',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_dark_mode' => 'boolean',
        'otp_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function isPembeli()
    {
        return $this->role === 'pembeli';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isOtpExpired()
    {
        return $this->otp_expires_at && $this->otp_expires_at->isPast();
    }

    // JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
