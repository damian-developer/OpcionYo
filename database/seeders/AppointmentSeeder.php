<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Schedule;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener los 3 médicos
        $doctors = Doctor::take(3)->get();
        // Obtener algunos pacientes (puedes cambiar esto según tu estructura)
        $patients = Patient::all();

        // Fechas para los próximos 2 meses
        $startDate = Carbon::now()->startOfMonth(); // Inicio del mes actual
        $endDate = (clone $startDate)->addMonths(2)->endOfMonth(); // Fin del mes siguiente

        foreach ($doctors as $doctor) {
            $currentMonth = clone $startDate;

            while ($currentMonth <= $endDate) {
                // Primera semana del mes
                $firstWeekStart = (clone $currentMonth)->startOfMonth();
                $firstWeekEnd = (clone $firstWeekStart)->addDays(6);

                // Obtener horarios de ese médico en la primera semana
                $schedules = Schedule::where('doctor_id', $doctor->id)
                    ->whereBetween('day', [$firstWeekStart->format('Y-m-d'), $firstWeekEnd->format('Y-m-d')])
                    ->get();

                // Generar 8 citas por doctor en la primera semana
                $appointmentsCreated = 0;
                foreach ($schedules as $schedule) {
                    if ($appointmentsCreated >= 8) break;

                    $date = Carbon::parse($schedule->day);

                    // Horario disponible sin contar la hora de almuerzo
                    $availableHours = [
                        Carbon::parse($schedule->start),
                        Carbon::parse($schedule->end)
                    ];

                    $breakStart = Carbon::parse($schedule->break_start);
                    $breakEnd = Carbon::parse($schedule->break_end);

                    $hour = $availableHours[0]; // Inicio del turno
                    while ($hour < $availableHours[1] && $appointmentsCreated < 8) {
                        // Saltar la hora de almuerzo
                        if ($hour->between($breakStart, $breakEnd, true)) {
                            $hour->addHour();
                            continue;
                        }

                        // Elegir un paciente aleatorio
                        $patient = $patients->random();

                        Appointment::create([
                            'doctor_id' => $doctor->id,
                            'patient_id' => $patient->id,
                            'date' => $schedule->day,
                            'start' => $hour->format('H:i:s'),
                            'end' => $hour->addHour()->format('H:i:s'),
                            'status' => 'booked', // Puedes cambiar esto si tienes otros estados
                        ]);

                        $appointmentsCreated++;
                        $hour->addHour(); // Avanzar a la siguiente hora
                    }
                }

                // Avanzar al siguiente mes
                $currentMonth->addMonth();
            }
        }
    }
}
