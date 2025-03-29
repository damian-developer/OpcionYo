<?php

namespace Database\Seeders;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SpecialtySeeder::class,
            UserSeeder::class,
            PatientSeeder::class,
            ScheduleSeeder::class,
            AppointmentSeeder::class
        ]);
    }
}
