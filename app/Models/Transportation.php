<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transportation extends Model
{
    use HasFactory;
    protected $fillable=['name','description','city_id','sunday','monday','tuesday','wednesday','thursday','friday','saturday','lines_img','prices','transportation_img'];
}
