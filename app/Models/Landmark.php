<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Landmark extends Model
{
    use HasFactory;
    protected $fillable = [
        'name','description','rating',
        'location','tourism_type','sunday',
        'monday','tuesday','wednesday','thursday',
        'friday','saturday','egyptian_ticket','egyptian_student_ticket',
        'foreign_ticket','foreign_student_ticket','booking','city_id','region'
    ];
}
