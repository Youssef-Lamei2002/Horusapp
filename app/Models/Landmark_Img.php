<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Landmark_Img extends Model
{
    use HasFactory;
    protected $fillable = ['img','landmark_id'];
}
