<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['day', 'start', 'end', 'break_start', 'break_end', 'doctor_id'];
}
