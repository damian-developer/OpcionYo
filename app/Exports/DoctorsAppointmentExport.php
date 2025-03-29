<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DoctorsAppointmentExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $doctors;
    protected $date;

    public function __construct($doctors, $date)
    {
        $this->doctors = $doctors;
        $this->date = $date; // Fecha para filtrar las citas
    }

    public function collection()
    {
        return $this->doctors;
    }

    /**
     * Mapeo de los doctores y sus appointments.
     */
    public function map($doctor): array
    {
        // Filtrar las citas por la fecha proporcionada
        $appointments = $doctor->appointments->where('date', $this->date)->map(function ($appointment) {
            return $appointment->start . ' - ' . $appointment->end;
        })->implode(', ');

        // Obtener los horarios libres para la fecha proporcionada
        $freeSchedules = collect($doctor->getFreeSchedules($this->date))->map(function ($schedule) {
            return $schedule['start'] . ' - ' . $schedule['end'];
        })->implode(', ');

        return [
            $doctor->name,
            $doctor->surname,
            $freeSchedules,
            $appointments,
        ];
    }

    /**
     * Encabezados de las columnas.
     */
    public function headings(): array
    {
        return [
            'Nombre',
            'Apellido',
            'Horarios Libres',
            'Horarios Ocupados',
        ];
    }
}
