<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentsTableSeeder extends Seeder
{
    public function run()
    {
        $programs = ['BSIT', 'BLIS', 'BSCS', 'BSIS'];
        $years = [1, 2, 3, 4];
        $sections = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $genders = ['male', 'female'];

        foreach ($programs as $program) {
            foreach ($years as $year) {
                foreach ($sections as $section) {
                    for ($i = 1; $i <= 5; $i++) {
                        DB::table('students')->insert([
                            'student_number' => 'S' . Str::random(6), // Unique student number
                            'first_name' => 'FirstName' . $i,
                            'middle_name' => 'MiddleName' . $i,
                            'last_name' => 'LastName' . $i,
                            'program' => $program,
                            'year' => $year,
                            'section' => $section,
                            'gender' => $genders[array_rand($genders)],
                            'pc_number' => rand(1000, 9999), // Random PC number
                            'rfid' => rand(1000000000, 1999999999), // Random RFID number
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
