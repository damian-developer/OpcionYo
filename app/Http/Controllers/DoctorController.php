<?php

namespace App\Http\Controllers;

use App\Exports\DoctorsAppointmentExport;
use App\Http\Requests\AvailableDoctorsRequest;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DoctorController extends Controller
{
    /**
     * Exporta los doctores, sus citas y horarios libres para una fecha específica.
     */
    public function exportDoctorsSchedule(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);
        $date = $request['date'];
        if (!$date) {
            return response()->json(['error' => 'La fecha es requerida'], 400);
        }

        $doctors = Doctor::with(['appointments', 'schedules'])->get();

        return Excel::download(new DoctorsAppointmentExport($doctors, $date), 'doctors-appointment-' . $date . '.xlsx');
    }

    /**
     * Obtiene los doctores disponibles en base a una fecha y hora específica.
     */
    public function avalaibleDoctors(AvailableDoctorsRequest $request)
    {
        $validated = $request->validated();

        $date = $validated['date'];
        $time = $validated['time'];

        if (!$date || !$time) {
            return response()->json(['error' => 'La fecha y la hora son requeridas'], 400);
        }


        $doctors = Doctor::with(['appointments', 'schedules'])->get();

        $freeSchedules = $doctors->map(function ($doctor) use ($date, $time) {

            $filteredSchedules = collect($doctor->getFreeSchedules($date))->filter(function ($schedule) use ($time) {
                $start = Carbon::createFromFormat('H:i', $schedule['start']);
                $requestedTime = Carbon::createFromFormat('H:i', $time);

                return $requestedTime->isSameHour($start);
            });

            return [
                'doctor_id' => $doctor->id,
                'name' => $doctor->name,
                'surname' => $doctor->surname,
                'free_schedules' => $filteredSchedules->values(),
            ];
        });


        $filteredDoctors = $freeSchedules->filter(function ($doctor) {
            return $doctor['free_schedules']->isNotEmpty();
        });

        return response()->json([
            'success' => true,
            'data' => $filteredDoctors->values(),
        ]);
    }
}
