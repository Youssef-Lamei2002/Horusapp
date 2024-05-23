<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tourguide_Language extends Model
{
    use HasFactory;
    protected $fillable = ['tourguide_id','language_id'];
}