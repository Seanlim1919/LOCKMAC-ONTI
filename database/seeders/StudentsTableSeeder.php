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

        // Calculate the total number of students
        $totalStudents = count($programs) * count($years) * count($sections) * 30;

        $rfidIds = [];

        // Populate the `rfids` table with enough entries
        for ($i = 1; $i <= $totalStudents; $i++) {
            $rfidId = DB::table('rfids')->insertGetId([
                'rfid_code' => Str::random(10), // Generate a random RFID code
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $rfidIds[] = $rfidId;
        }

        $rfidIndex = 0;

        // Assign each student a unique RFID
        foreach ($programs as $program) {
            foreach ($years as $year) {
                foreach ($sections as $section) {
                    $pcNumbers = range(1, 30); // Create an array of PC numbers from 1 to 30
                    shuffle($pcNumbers); // Shuffle to randomize the PC assignment

                    for ($i = 1; $i <= 30; $i++) { // Limit each section to 30 students
                        DB::table('students')->insert([
                            'student_number' => 'S' . Str::random(6), // Unique student number
                            'first_name' => 'FirstName' . $i,
                            'middle_name' => 'MiddleName' . $i,
                            'last_name' => 'LastName' . $i,
                            'program' => $program,
                            'year' => $year,
                            'section' => $section,
                            'gender' => $genders[array_rand($genders)],
                            'pc_number' => $pcNumbers[$i - 1], // Assign a unique PC number within the section
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
