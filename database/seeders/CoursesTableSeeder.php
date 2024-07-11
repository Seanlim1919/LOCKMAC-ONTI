<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursesTableSeeder extends Seeder
{
    public function run()
    {
        $courses = [
            ['course_name' => 'Systems Integration and Architecture 2', 'course_code' => 'ITEC 324', 'description' => 'Systems Integration and Architecture 2', 'program' => 'BSIT'],
            ['course_name' => 'Social and Professional Issues', 'course_code' => 'IT 329', 'description' => 'Social and Professional Issues', 'program' => 'BSIT'],
            ['course_name' => 'English Proficiency Program', 'course_code' => 'ITA 3210', 'description' => 'English Proficiency Program', 'program' => 'BSIT'],
            ['course_name' => 'Application Development and Emerging Technologies', 'course_code' => 'CCIT 106', 'description' => 'Application Development and Emerging Technologies', 'program' => 'BSIT'],
            ['course_name' => 'Ethical Hacking', 'course_code' => 'ITA 327', 'description' => 'Ethical Hacking', 'program' => 'BSIT'],
            ['course_name' => 'Indigenous Creative Crafts', 'course_code' => 'GE ELECT 7', 'description' => 'Indigenous Creative Crafts', 'program' => 'BSIT'],
            ['course_name' => 'Information Assurance and Security 2', 'course_code' => 'IT 3210', 'description' => 'Information Assurance and Security 2', 'program' => 'BSIT'],
            ['course_name' => 'Capstone Project 1', 'course_code' => 'IT 3211', 'description' => 'Capstone Project 1', 'program' => 'BSIT'],
        ];

        DB::table('courses')->insert($courses);
    }
}
