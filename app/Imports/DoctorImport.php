<?php

namespace App\Imports;

use App\Models\Doctor;
use App\Services\DoctorBannerService;
use Illuminate\Support\Facades\Log;
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

    public function __construct(
        private readonly DoctorBannerService $bannerService,
    ) {
    }

    public function model(array $row)
    {
        $mslCode = trim($row['msl_code'] ?? '');
        $doctorName = trim($row['doctor_name'] ?? '');
        $speciality = trim($row['speciality'] ?? $row['specialty'] ?? '');

        if ($mslCode === '' || $doctorName === '') {
            Log::warning('Doctor import row skipped because MSL code or doctor name is empty.', [
                'msl_code' => $mslCode,
                'doctor_name' => $doctorName,
            ]);

            $this->skippedCount++;

            return null;
        }

        $existing = Doctor::where('msl_code', $mslCode)->first();

        if ($existing) {
            $oldDoctorName = $existing->doctor_name;
            $oldSpeciality = $existing->speciality;
            $newSpeciality = $speciality !== '' ? $speciality : null;
            $textChanged = $oldDoctorName !== $doctorName || $oldSpeciality !== $newSpeciality;

            Log::info('Doctor import update - MSL match.', [
                'doctor_id' => $existing->id,
                'msl_code' => $mslCode,
                'old_doctor_name' => $oldDoctorName,
                'new_doctor_name' => $doctorName,
                'old_speciality' => $oldSpeciality,
                'new_speciality' => $newSpeciality,
            ]);

            $existing->update([
                'doctor_name' => $doctorName,
                'speciality' => $newSpeciality,
            ]);

            if ($textChanged && $existing->banner_path) {
                try {
                    $this->bannerService->refreshBannerText($existing->fresh('employee'));
                } catch (Throwable $exception) {
                    Log::warning('Doctor data updated, but banner text refresh failed.', [
                        'doctor_id' => $existing->id,
                        'banner_path' => $existing->banner_path,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }

            $this->updatedCount++;

            return null;
        }

        Log::warning('Doctor import row skipped because MSL code was not found.', [
                'msl_code' => $mslCode,
        ]);

        $this->skippedCount++;

        return null;
    }

    public function onError(Throwable $e)
    {
        Log::error('Doctor Import Error', [
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
