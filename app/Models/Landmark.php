<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Landmark extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'rating',
        'location',
        'tourism_type',
        'sunday_open',
        'sunday_close',
        'monday_open',
        'monday_close',
        'tuesday_open',
        'tuesday_close',
        'wednesday_open',
        'wednesday_close',
        'thursday_open',
        'thursday_close',
        'friday_open',
        'friday_close',
        'saturday_open',
        'saturday_close',
        'egyptian_ticket',
        'egyptian_student_ticket',
        'foreign_ticket',
        'foreign_student_ticket',
        'booking',
        'region',
        'city_id',
        'needTourguide'
    ];
}
