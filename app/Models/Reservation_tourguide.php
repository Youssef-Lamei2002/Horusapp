<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation_tourguide extends Model
{
    protected  $fillable = ['tourist_id','tourguide_id','landmark_id','price_of_hour','hours','isFinished','isAccepted','starting_time','finished_time','day']; 
    use HasFactory;
    public function tourist()
{
    return $this->belongsTo(Tourist::class); // Assuming Tourist is the related model
}
public function tourguide()
{
    return $this->belongsTo(Tourguide::class);
}
public function landmark()
{
    return $this->belongsTo(Landmark::class); 
}
}