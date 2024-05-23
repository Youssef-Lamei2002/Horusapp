<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel_Img extends Model
{
    use HasFactory;
    protected $fillable = ['img','hotel_id'];
}
