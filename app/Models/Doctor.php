<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Doctor extends Model
{
    protected $fillable = [
        'name',
        'surname',
        'identification',
        'address',
        'phone',
        'country',
        'timezone',
        'specialty_id',
        'user_id'
    ];

    /**
     * Calcula los horarios libres del doctor eliminando los horarios ocupados por citas.
     *
     * @param string $date Fecha en formato YYYY-MM-DD
     * @return array
     */
    public function getFreeSchedules($date)
    {
        $schedules = $this->schedules->where('day', $date);
        $appointments = $this->appointments->where('date', $date);

        $freeSchedules = [];

        foreach ($schedules as $schedule) {
            $workStart = Carbon::createFromFormat('H:i:s', $schedule->start);
            $workEnd = Carbon::createFromFormat('H:i:s', $schedule->end);
            $breakStart = Carbon::createFromFormat('H:i:s', $schedule->break_start);
            $breakEnd = Carbon::createFromFormat('H:i:s', $schedule->break_end);

            $currentHour = (clone $workStart);

            while ($currentHour < $workEnd) {
                $nextHour = (clone $currentHour)->addHour();

                // Verificar si el horario está dentro del almuerzo
                if (
                    $currentHour->between($breakStart, $breakEnd, false) ||
                    $nextHour->between($breakStart, $breakEnd, true)
                ) {
                    $currentHour = $nextHour;
                    continue;
                }

                $isBooked = $appointments->contains(function ($appointment) use ($currentHour, $nextHour) {
                    $appointmentStart = Carbon::createFromFormat('H:i:s', $appointment->start);
                    return $currentHour->equalTo($appointmentStart);
                });

                if (!$isBooked) {
                    $freeSchedules[] = [
                        'start' => $currentHour->format('H:i'),
                        'end' => $nextHour->format('H:i'),
                    ];
                }

                $currentHour = $nextHour;
            }
        }

        return $freeSchedules;
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }
}
