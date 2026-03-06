<?php

namespace App\Imports;

use App\Models\Doctor;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DoctorImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $employee = Employee::where('position_code', $row['position_code'])->first();

        if (!$employee) {
            return null;
        }

        return new Doctor([
            'employee_id' => $employee->id,
            'doctor_name' => $row['doctor_name'],
            'msl_code' => $row['msl_code'],
        ]);
    }
}
