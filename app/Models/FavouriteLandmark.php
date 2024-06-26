<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouriteLandmark extends Model
{
    protected $fillable = [
        'tourist_id',
        'landmark_id',
    ];
    use HasFactory;
    public function landmark()
{
    return $this->belongsTo(Landmark::class);
}
}

