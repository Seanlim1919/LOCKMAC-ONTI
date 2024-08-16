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

        $rfidIds = [];

        // First, populate the `rfids` table with enough entries
        for ($i = 1; $i <= 650; $i++) {
            $rfidId = DB::table('rfids')->insertGetId([
                'rfid_code' => Str::random(10), // Generate a random RFID code
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $rfidIds[] = $rfidId;
        }

        $rfidIndex = 0;

        // Then, assign each student a unique RFID
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
                            'rfid_id' => $rfidIds[$rfidIndex], // Assign the RFID
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $rfidIndex++;
                    }
                }
            }
        }
    }
}
