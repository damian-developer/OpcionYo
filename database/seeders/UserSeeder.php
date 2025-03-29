<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('users')->insert([
            [
                'name' => 'Juan Pérez',
                'email' => 'juan@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'María Gómez',
                'email' => 'maria@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Luis Ramírez',
                'email' => 'luis@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        DB::table('doctors')->insert([
            [
                'name' => 'Juan',
                'surname' => 'Pérez',
                'identification' => '12345678',
                'address' => fake()->address(),
                'phone' => fake()->phoneNumber(),
                'user_id' => 1,
                'specialty_id' => 1,
                'country' => fake()->country(),
                'timezone' => 'America/Argentina/Buenos_Aires',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'María',
                'surname' => 'Gómez',
                'identification' => '12345678',
                'address' => fake()->address(),
                'phone' => fake()->phoneNumber(),
                'user_id' => 2,
                'specialty_id' => 2,
                'country' => fake()->country(),
                'timezone' => 'Europe/Madrid',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Luis',
                'surname' => 'Ramírez',
                'identification' => '12345678',
                'address' => fake()->address(),
                'phone' => fake()->phoneNumber(),
                'user_id' => 3,
                'specialty_id' => 3,
                'country' => fake()->country(),
                'timezone' => 'America/Caracas',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
