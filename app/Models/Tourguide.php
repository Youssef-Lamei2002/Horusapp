<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Tourguide extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'nationality',
        'phone_number',
        'profile_pic',
        'ssn',
        'email_type',
        'isBlocked',
        'isApproved',
        'rate',
        'price',
        'city_id',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    protected $hidden = [
        'password',
    ];
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'tourguide__languages');
    }
    public function reservations()
    {
        return $this->hasMany(Reservation_tourguide::class);
    }
}