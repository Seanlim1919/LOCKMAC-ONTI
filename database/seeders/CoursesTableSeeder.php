<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursesTableSeeder extends Seeder
{
    public function run()
    {
        $courses = [
            ['course_name' => 'Systems Integration and Architecture 2', 'course_code' => 'ITEC 324', 'sem_avail' => 'First', 'program' => 'BSIT', 'year_avail' => 2],
            ['course_name' => 'Social and Professional Issues', 'course_code' => 'IT 329', 'sem_avail' => 'Second', 'program' => 'BSIT', 'year_avail' => 2],
            ['course_name' => 'English Proficiency Program', 'course_code' => 'ITA 3210', 'sem_avail' => 'First', 'program' => 'BSIT', 'year_avail' => 1],
            ['course_name' => 'Application Development and Emerging Technologies', 'course_code' => 'CCIT 106', 'sem_avail' => 'Second', 'program' => 'BSIT', 'year_avail' => 1],
            ['course_name' => 'Ethical Hacking', 'course_code' => 'ITA 327', 'sem_avail' => 'First', 'program' => 'BSIT', 'year_avail' => 2],
            ['course_name' => 'Indigenous Creative Crafts', 'course_code' => 'GE ELECT 7', 'sem_avail' => 'Second', 'program' => 'BSIT', 'year_avail' => 1],
            ['course_name' => 'Information Assurance and Security 2', 'course_code' => 'IT 3210', 'sem_avail' => 'First', 'program' => 'BSIT', 'year_avail' => 2],
            ['course_name' => 'Capstone Project 1', 'course_code' => 'IT 3211', 'sem_avail' => 'Second', 'program' => 'BSIT', 'year_avail' => 4],
        ];

        DB::table('courses')->insert($courses);
    }
}
