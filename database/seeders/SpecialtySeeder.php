<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('specialties')->insert([
            [
                'specialties' => 'Psicología',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'specialties' => 'Psiquiatria',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'specialties' => 'Nutrición',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
