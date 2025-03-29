<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Luiz Lopez',
                'email' => 'luiz@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Concepcion Garcia',
                'email' => 'concepcion@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Antonia Ramirez',
                'email' => 'antonia@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        DB::table('patients')->insert([
            [
                'name' => 'Luiz',
                'surname' => 'Lopez',
                'user_id' => 4,
                'timezone' => 'America/Bolivia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Concepcion',
                'surname' => 'Garcia',
                'user_id' => 5,
                'timezone' => 'America/Mexico',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Antonia',
                'surname' => 'Ramírez',
                'user_id' => 6,
                'timezone' => 'America/Colombia',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
