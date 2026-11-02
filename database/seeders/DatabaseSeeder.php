<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * =========================================================
         * DEMO ADMIN
         * =========================================================
         */

        User::updateOrCreate(
            [
                'email' => 'admin@project.com',
            ],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );


        /*
         * =========================================================
         * DEMO CONSULTANTS
         * =========================================================
         */

        User::updateOrCreate(
            [
                'email' => 'sarah@project.com',
            ],
            [
                'name' => 'Sarah Ahmed',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CONSULTANT,
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'ali@project.com',
            ],
            [
                'name' => 'Ali Raza',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CONSULTANT,
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'hina@project.com',
            ],
            [
                'name' => 'Hina Malik',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CONSULTANT,
            ]
        );


        /*
         * =========================================================
         * UNIVERSITY + PUBLIC CONSULTANT DIRECTORY
         * =========================================================
         */

        $this->call([
            UniversitySeeder::class,
            ConsultantSeeder::class,
        ]);
    }
}