<?php

namespace App\Imports;

use App\Models\Doctor;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DoctorImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    private int $updatedCount = 0;
    private int $skippedCount = 0;

    public function collection(Collection $rows): void
    {
        $recordsByMsl = [];

        foreach ($rows as $row) {
            $mslCode = trim((string) ($row['msl_code'] ?? ''));
            $doctorName = trim((string) ($row['doctor_name'] ?? ''));
            $speciality = trim((string) ($row['speciality'] ?? $row['specialty'] ?? ''));

            if ($mslCode === '' || $doctorName === '') {
                $this->skippedCount++;
                continue;
            }

            $recordsByMsl[$mslCode] = [
                'doctor_name' => $doctorName,
                'speciality' => $speciality !== '' ? $speciality : null,
            ];
        }

        if ($recordsByMsl === []) {
            return;
        }

        $existingDoctors = Doctor::query()
            ->whereIn('msl_code', array_keys($recordsByMsl))
            ->get(['id', 'employee_id', 'msl_code', 'created_at'])
            ->keyBy(fn (Doctor $doctor) => (string) $doctor->msl_code);

        $updates = [];
        $updatedAt = now();

        foreach ($recordsByMsl as $mslCode => $record) {
            $doctor = $existingDoctors->get($mslCode);

            if (!$doctor) {
                $this->skippedCount++;
                continue;
            }

            $updates[] = [
                'id' => $doctor->id,
                'employee_id' => $doctor->employee_id,
                'msl_code' => $doctor->msl_code,
                'doctor_name' => $record['doctor_name'],
                'speciality' => $record['speciality'],
                'created_at' => $doctor->created_at,
                'updated_at' => $updatedAt,
            ];
        }

        if ($updates !== []) {
            Doctor::upsert(
                $updates,
                ['id'],
                ['doctor_name', 'speciality', 'updated_at'],
            );

            $this->updatedCount += count($updates);
        }
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
