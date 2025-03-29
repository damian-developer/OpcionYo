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
            event(new NewAppointment($appointment));
        });
    }

    public function AvailableTimes($doctors)
    {
        return $doctors->map(function ($doctor) {
            $availableTimes = [];
            $reservedTimes = [];

            foreach ($doctor->schedules as $schedule) {
                $date = $schedule->day;

                // Horario de trabajo del doctor
                $workStart = Carbon::createFromFormat('H:i:s', $schedule->start);
                $workEnd = Carbon::createFromFormat('H:i:s', $schedule->end);
                $breakStart = Carbon::createFromFormat('H:i:s', $schedule->break_start);
                $breakEnd = Carbon::createFromFormat('H:i:s', $schedule->break_end);


                $appointmentsForDay = $doctor->appointments
                    ->where('date', $date)
                    ->map(function ($appointment) {
                        return [
                            'start' => Carbon::createFromFormat('H:i:s', $appointment->start),
                            'end' => Carbon::createFromFormat('H:i:s', $appointment->end)
                        ];
                    })
                    ->toArray();

                // Generar franjas horarias
                $currentHour = (clone $workStart);

                while ($currentHour < $workEnd) {
                    $nextHour = (clone $currentHour)->addHour();
                    $timeSlot = [
                        'date' => $date,
                        'start' => $currentHour->format('H:i'),
                        'end' => $nextHour->format('H:i'),
                    ];

                    // Verificar horario de descanso y turno
                    $isBreakTime = $currentHour->between($breakStart, $breakEnd, false) ||
                        $nextHour->between($breakStart, $breakEnd, true) ||
                        ($currentHour <= $breakStart && $nextHour >= $breakEnd);

                    $isBooked = $this->isTimeSlotBooked($currentHour, $nextHour, $appointmentsForDay);

                    if ($isBreakTime) {
                    } elseif ($isBooked) {
                        $reservedTimes[] = $timeSlot;
                    } else {
                        $availableTimes[] = $timeSlot;
                    }

                    $currentHour = $nextHour;
                }
            }

            return [
                'doctor_id' => $doctor->id,
                'name' => $doctor->name ?? 'Doctor #' . $doctor->id,
                'available_times' => $availableTimes,
                'reserved_times' => $reservedTimes,
            ];
        })->filter(function ($doctor) {
            return count($doctor['available_times']) > 0;
        })->values();
    }

    /**
     * Determina si un turno está reservado.
     */
    private function isTimeSlotBooked($currentHour, $nextHour, $appointments)
    {
        foreach ($appointments as $appointment) {
            $appointmentStart = $appointment['start'];
            $appointmentEnd = $appointment['end'];

            if (
                ($currentHour->format('H:i:s') === $appointmentStart->format('H:i:s') &&
                    $nextHour->format('H:i:s') === $appointmentEnd->format('H:i:s')) ||
                ($currentHour->between($appointmentStart, $appointmentEnd->subSecond(), true)) ||
                ($nextHour->between($appointmentStart->addSecond(), $appointmentEnd, true)) ||
                ($currentHour <= $appointmentStart && $nextHour >= $appointmentEnd)
            ) {
                return true;
            }
        }

        return false;
    }
    public function getTimeAttribute($value)
    {
        $timezone = $this->user->timezone ?? 'America/New_York'; // Zona horaria del usuario
        return Carbon::parse($value, 'America/New_York')->setTimezone($timezone);
    }
}
