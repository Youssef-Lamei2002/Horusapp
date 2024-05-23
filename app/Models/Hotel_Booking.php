<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel_Booking extends Model
{
    use HasFactory;
    protected $fillable = ['booking_link','booking_img_id','hotel_id'];
}

