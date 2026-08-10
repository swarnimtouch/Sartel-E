<?php

namespace App\Imports;

use App\Models\Doctor;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Throwable;

class DoctorImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    private int $insertedCount = 0;
    private int $updatedCount = 0;
    private int $skippedCount = 0;

    public function model(array $row)
    {
        $positionCode = trim($row['position_code'] ?? '');
        $mslCode = trim($row['msl_code'] ?? '');
        $doctorName = trim($row['doctor_name'] ?? '');

        $employee = Employee::where('position_code', $positionCode)->first();

        if (!$employee) {
            \Log::warning(
                'Employee not found. Position Code: ' . $positionCode
            );

            $this->skippedCount++;

            return null;
        }

        $existing = Doctor::where('employee_id', $employee->id)
            ->where('msl_code', $mslCode)
            ->first();

        if ($existing) {

    \Log::info('Doctor UPDATE - MSL MATCH', [
        'excel_position_code' => $positionCode,
        'employee_id'         => $employee->id,
        'doctor_id'           => $existing->id,
        'old_doctor_name'     => $existing->doctor_name,
        'new_doctor_name'     => $doctorName,
        'old_msl_code'        => $existing->msl_code,
        'new_msl_code'        => $mslCode,
    ]);

    $existing->timestamps = false;

    $updated = $existing->update([
        'doctor_name' => $doctorName,
        'msl_code' => $mslCode,
    ]);

    \Log::info('Doctor UPDATE RESULT', [
        'doctor_id' => $existing->id,
        'updated'   => $updated,
    ]);

    $this->updatedCount++;

    return null;
}

        $existingByName = Doctor::where('employee_id', $employee->id)
            ->where('doctor_name', $doctorName)
            ->first();

        if ($existingByName) {

    \Log::info('Doctor UPDATE - NAME MATCH', [
        'excel_position_code' => $positionCode,
        'employee_id'         => $employee->id,
        'doctor_id'           => $existingByName->id,
        'doctor_name'         => $doctorName,
        'old_msl_code'        => $existingByName->msl_code,
        'new_msl_code'        => $mslCode,
    ]);

    $existingByName->timestamps = false;

    $updated = $existingByName->update([
        'msl_code' => $mslCode,
    ]);

    \Log::info('Doctor UPDATE RESULT', [
        'doctor_id' => $existingByName->id,
        'updated'   => $updated,
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

    public function onError(Throwable $e)
    {
        \Log::error('Doctor Import Error', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        $this->skippedCount++;
    }

    public function getInsertedCount(): int
    {
        return $this->insertedCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}