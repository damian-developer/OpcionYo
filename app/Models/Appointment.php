<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Events\NewAppointment;

class Appointment extends Model
{
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'date',
        'start',
        'end',
        'status',
    ];

    protected static function booted()
    {
        static::created(function ($appointment) {
            event(new NewAppointment($appointment->with(['doctor'])->first()));
        });
    }

    public function getTimeAttribute($value)
    {
        $timezone = $this->user->timezone ?? 'America/New_York'; // Zona horaria del usuario
        return Carbon::parse($value, 'America/New_York')->setTimezone($timezone);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
