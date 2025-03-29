<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $doctors = Doctor::take(3)->get();

        $startDate = Carbon::now()->startOfMonth(); // Inicio del mes actual
        $endDate = (clone $startDate)->addMonths(2)->endOfMonth(); // Fin del mes siguiente

        $workStart = '09:00:00';
        $lunchStart = '13:00:00';
        $lunchEnd = '14:00:00';
        $workEnd = '17:00:00';

        foreach ($doctors as $doctor) {
            $date = clone $startDate;

            while ($date <= $endDate) {
                if ($date->isWeekday()) {
                    Schedule::create([
                        'doctor_id' => $doctor->id,
                        'day' => $date->format('Y-m-d'),
                        'start' => $workStart,
                        'break_start' => $lunchStart,
                        'break_end' => $lunchEnd,
                        'end' => $workEnd,
                    ]);
                }
                $date->addDay();
            }
        }
    }
}
