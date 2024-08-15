<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Insert admin user
        DB::table('users')->insert([
            'first_name' => 'Marylie',
            'middle_name' => 'Lorzano',
            'last_name' => 'Refereza',
            'email' => 'marefereza@cspc.edu.ph',
            'password' => '$2y$10$XAOHRuztBgo9QpxX7WQN0.4HtG3TK5UH/5QETp2458WCyxP0RZziy', // bcrypt password
            'phone_number' => '09915135750',
            'gender' => 'female',
            'date_of_birth' => '2003-08-01',
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert faculty user
        DB::table('users')->insert([
            'first_name' => 'John Francis',
            'middle_name' => 'Cambiado',
            'last_name' => 'Carpo',
            'email' => 'jocarpo@cspc.edu.ph',
            'password' => '$2y$10$XAOHRuztBgo9QpxX7WQN0.4HtG3TK5UH/5QETp2458WCyxP0RZziy', // bcrypt password
            'phone_number' => '09123456789',
            'gender' => 'male',
            'date_of_birth' => '2003-02-01',
            'role' => 'faculty',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'first_name' => 'April',
            'middle_name' => 'Botor',
            'last_name' => 'Soreta',
            'email' => 'apsoreta@cspc.edu.ph',
            'password' => '$2y$10$XAOHRuztBgo9QpxX7WQN0.4HtG3TK5UH/5QETp2458WCyxP0RZziy', // bcrypt password
            'phone_number' => '09123456788',
            'gender' => 'female',
            'date_of_birth' => '2002-02-01',
            'role' => 'faculty',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
