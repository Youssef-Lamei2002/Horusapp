<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation_tourguide extends Model
{
    protected  $fillable = ['tourist_id','tourguide_id','landmark_id','price_hour','hours','commission','isAccepted','starting_time','finished_time','day']; 
    use HasFactory;
}
