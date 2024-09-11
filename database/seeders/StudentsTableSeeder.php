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

        $totalStudents = count($programs) * count($years) * count($sections) * 30;

        $rfidIds = [];

        for ($i = 1; $i <= $totalStudents; $i++) {
            $rfidId = DB::table('rfids')->insertGetId([
                'rfid_code' => Str::random(10), 
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $rfidIds[] = $rfidId;
        }

        $rfidIndex = 0;

        foreach ($programs as $program) {
            foreach ($years as $year) {
                foreach ($sections as $section) {
                    $pcNumbers = range(1, 30);
                    shuffle($pcNumbers); 

                    for ($i = 1; $i <= 30; $i++) { 
                        DB::table('students')->insert([
                            'student_number' => 'S' . Str::random(6), 
                            'first_name' => 'FirstName' . $i,
                            'middle_name' => 'MiddleName' . $i,
                            'last_name' => 'LastName' . $i,
                            'program' => $program,
                            'year' => $year,
                            'section' => $section,
                            'gender' => $genders[array_rand($genders)],
                            'pc_number' => $pcNumbers[$i - 1], 
                            'rfid_id' => $rfidIds[$rfidIndex], 
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
