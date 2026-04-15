<?php

namespace App\Imports;

use App\Models\Doctor;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class DoctorImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    private int $insertedCount = 0;
    private int $updatedCount = 0;

    public function model(array $row)
    {
        $positionCode = (int)trim($row['position_code'] ?? '');
        $mslCode = trim($row['msl_code'] ?? '');
        $doctorName = trim($row['doctor_name'] ?? '');

        $employee = Employee::where('position_code', $positionCode)->first();

        if (!$employee) {
            \Log::warning('Employee not found for position_code: ' . $positionCode);
            $this->skippedCount++;
            return null;
        }

        $existing = Doctor::where('employee_id', $employee->id)
            ->where('msl_code', $mslCode)
            ->first();

        if ($existing) {
            $existing->timestamps = false;
            $existing->update([
                'doctor_name' => $doctorName,
                'msl_code' => $mslCode,
            ]);
            $this->updatedCount++;
            return null;
        }


        $existingByName = Doctor::where('employee_id', $employee->id)
            ->where('doctor_name', $doctorName)
            ->first();

        if ($existingByName) {
            $existingByName->timestamps = false;
            $existingByName->update([
                'msl_code' => $mslCode,
            ]);
            $this->updatedCount++;
            return null;
        }


        $this->insertedCount++;

        return new Doctor([
            'employee_id' => $employee->id,
            'doctor_name' => $doctorName,
            'msl_code' => $mslCode,
        ]);
    }

    public function getInsertedCount(): int
    {
        return $this->insertedCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }
}
