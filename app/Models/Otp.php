<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable =['tourist_id', 'tourguide_id', 'otp', 'expires_at'];

}