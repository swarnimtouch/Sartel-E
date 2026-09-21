<?php

namespace App\Imports;

use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Throwable;

class EmployeeImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    private int $insertedCount = 0;
    private int $updatedCount = 0;
    private int $skippedCount = 0;

    public function model(array $row)
    {
        // Clean array keys: lowercase and trimmed
        $cleanRow = [];
        foreach ($row as $k => $v) {
            $cleanKey = strtolower(trim((string) $k));
            $cleanKey = str_replace([' ', '-'], '_', $cleanKey);
            $cleanRow[$cleanKey] = is_string($v) ? trim($v) : $v;
        }

        $zone = $cleanRow['zone'] ?? $cleanRow['zone_name'] ?? null;
        $positionCode = $cleanRow['position_code'] ?? null;
        $employeeCode = $cleanRow['employee_code'] ?? null;
        $name = $cleanRow['name'] ?? $cleanRow['employee_name'] ?? null;
        $designationName = $cleanRow['designation_name'] ?? $cleanRow['designation'] ?? null;
        $hqName = $cleanRow['hq_name'] ?? $cleanRow['hq'] ?? null;
        $hqCode = $cleanRow['hq_code'] ?? null;

        // Skip completely empty rows
        if (!$positionCode && !$employeeCode && !$name) {
            $this->skippedCount++;
            return null;
        }

        // Search for existing employee by position_code first, then employee_code
        $employee = null;
        if ($positionCode) {
            $employee = Employee::where('position_code', $positionCode)->first();
        }
        if (!$employee && $employeeCode) {
            $employee = Employee::where('employee_code', $employeeCode)->first();
        }

        $dataToUpdate = [];
        if ($zone !== null && $zone !== '') $dataToUpdate['zone'] = $zone;
        if ($name !== null && $name !== '') $dataToUpdate['name'] = $name;
        if ($positionCode !== null && $positionCode !== '') $dataToUpdate['position_code'] = $positionCode;
        if ($employeeCode !== null && $employeeCode !== '') $dataToUpdate['employee_code'] = $employeeCode;
        if ($designationName !== null && $designationName !== '') $dataToUpdate['designation_name'] = $designationName;
        if ($hqName !== null && $hqName !== '') $dataToUpdate['hq_name'] = $hqName;
        if ($hqCode !== null && $hqCode !== '') $dataToUpdate['hq_code'] = $hqCode;

        if ($employee) {
            if (!empty($dataToUpdate)) {
                $employee->update($dataToUpdate);
            }
            $this->updatedCount++;
            return null;
        }

        // New employee
        if (!$name || (!$positionCode && !$employeeCode)) {
            $this->skippedCount++;
            return null;
        }

        $this->insertedCount++;

        return new Employee([
            'name' => $name,
            'position_code' => $positionCode ?: 0,
            'employee_code' => $employeeCode,
            'designation_name' => $designationName ?: 'BE',
            'hq_name' => $hqName ?: '',
            'hq_code' => $hqCode,
            'zone' => $zone,
        ]);
    }

    public function onError(Throwable $e)
    {
        Log::error('Employee Import Error', [
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
