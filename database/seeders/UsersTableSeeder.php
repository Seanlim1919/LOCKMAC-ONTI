<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
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
    }
}
