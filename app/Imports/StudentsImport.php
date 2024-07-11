<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;

class StudentsImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Student([
            'student_number' => $row[0],
            'first_name'     => $row[1],
            'last_name'      => $row[2],
            'program'        => $row[3],
            'year_and_section' => $row[4],
            'pc_number'      => $row[5],
        ]);
    }
}
