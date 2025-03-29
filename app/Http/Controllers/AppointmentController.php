<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvalaibleAppointmentRequest;
use App\Models\Doctor;
use App\Models\Appointment;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event as GoogleCalendarEvent;

class AppointmentController extends Controller
{

    /**
     * Para mayor escalabilidad este metodo deberia ser un event+listener
     */
    public function sendAppointmentToGoogleCalendar()
    {
        //logica para guardar el turno


        $appointment = Appointment::find(1);

        $date = $appointment->date;
        $startTime = $appointment->start;
        $endTime = $appointment->end;

        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', "$date $startTime");
        $endDateTime = Carbon::createFromFormat('Y-m-d H:i', "$date $endTime");

        GoogleCalendarEvent::create([
            'name' => 'Turno con ' . $appointment->doctor->name,
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
            'addAttendee' => ['email' => $appointment->patient->user->email],
        ]);
    }
    public function avalaibleAppointment(AvalaibleAppointmentRequest $request)
    {
        $validated = $request->validated();

        $date = $validated['date'];
        $start = $validated['startTime'];
        $end = $validated['endTime'];

        $doctors = Doctor::with(['appointments', 'schedules'])->get();

        $result = $doctors->map(function ($doctor) use ($date, $start, $end) {

            $freeSchedules = collect($doctor->getFreeSchedules($date))->filter(function ($schedule) use ($start, $end) {
                $scheduleStart = Carbon::createFromFormat('H:i', $schedule['start']);
                $requestedStart = Carbon::createFromFormat('H:i', $start);

                return $requestedStart->isSameHour($scheduleStart);
            });

            // Obtener los horarios reservados del doctor para la fecha especificada
            $reservedSchedules = $doctor->appointments->where('date', $date)->map(function ($appointment) use ($doctor) {
                $timezone = $doctor->timezone ?? 'UTC';
                return [
                    'start' => Carbon::createFromFormat('H:i:s', $appointment->start, 'UTC')->setTimezone($timezone)->format('H:i'),
                    'end' => Carbon::createFromFormat('H:i:s', $appointment->end, 'UTC')->setTimezone($timezone)->format('H:i'),
                ];
            });

            // Convertir los horarios libres a la zona horaria del doctor
            $timezone = $doctor->timezone ?? 'UTC';
            $freeSchedules = $freeSchedules->map(function ($schedule) use ($timezone) {
                return [
                    'start' => Carbon::createFromFormat('H:i', $schedule['start'], 'UTC')->setTimezone($timezone)->format('H:i'),
                    'end' => Carbon::createFromFormat('H:i', $schedule['end'], 'UTC')->setTimezone($timezone)->format('H:i'),
                ];
            });

            return [
                'doctor_id' => $doctor->id,
                'name' => $doctor->name,
                'surname' => $doctor->surname,
                'reserved_schedules' => $reservedSchedules->values(),
                'free_schedules' => $freeSchedules->values(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result->values(),
        ]);
    }
}
