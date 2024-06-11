<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'tourist_id',
        'tourguide_id',
        'reservation_id',
        'rate',
        'feedback',
    ];
    use HasFactory;
}
