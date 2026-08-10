<?php

namespace App\Services;

use App\Models\Doctor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class DoctorBannerService
{
    public function generate(Doctor $doctor): ?string
    {
        $templateName = match ($doctor->gender) {
            'Male' => 'male-template.jpg',
            'Female' => 'female-template.jpg',
            default => null,
        };

        if (!$templateName) {
            return null;
        }

        try {
            $template = imagecreatefromjpeg(public_path('images/doctor-banners/' . $templateName));

            if (!$template) {
                throw new RuntimeException('The banner template could not be read.');
            }

            $this->placeText($template, $doctor);

            ob_start();
            imagepng($template, null, 8);
            $bannerContents = ob_get_clean();

            imagedestroy($template);

            $employeeCode = $doctor->employee?->employee_code ?? 'emp_' . $doctor->employee_id;
            $employeeName = str($doctor->employee?->name ?: 'employee')->slug('_')->value();
            $doctorSlug = str($doctor->doctor_name)->slug('_')->value() ?: 'doctor';
            $path = "employee_{$employeeCode}_{$employeeName}/banners/{$doctorSlug}_{$doctor->id}_" . time() . '.png';

            Storage::disk('s3')->put($path, $bannerContents, [
                'visibility' => 'public',
                'ContentType' => 'image/png',
            ]);

            $oldBanner = $doctor->banner_path;
            $doctor->forceFill(['banner_path' => $path])->saveQuietly();

            if ($oldBanner && $oldBanner !== $path) {
                Storage::disk('s3')->delete($oldBanner);
            }

            return $path;
        } catch (Throwable $exception) {
            Log::error('Doctor banner generation failed.', [
                'doctor_id' => $doctor->id,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private function placeText(\GdImage $banner, Doctor $doctor): void
    {
        $nameFont = public_path('fonts/RobotoSlab-Bold.ttf');
        $specialityFont = public_path('fonts/RobotoSlab-Regular.ttf');
        $white = imagecolorallocate($banner, 255, 255, 255);
        $accent = imagecolorallocate($banner, 210, 242, 255);
        $shadow = imagecolorallocatealpha($banner, 0, 0, 0, 55);
        $name = $this->fitText($doctor->doctor_name, $nameFont, 70, 1800);
        $speciality = $this->fitText($doctor->speciality ?: 'Doctor', $specialityFont, 55, 1800);

        imagettftext($banner, 70, 0, 363, 2123, $shadow, $nameFont, $name);
        imagettftext($banner, 70, 0, 360, 2120, $white, $nameFont, $name);
        imagettftext($banner, 55, 0, 362, 2242, $shadow, $specialityFont, $speciality);
        imagettftext($banner, 55, 0, 360, 2240, $accent, $specialityFont, $speciality);
    }

    private function fitText(string $text, string $font, int $fontSize, int $maxWidth): string
    {
        $text = trim($text);
        while (mb_strlen($text) > 1) {
            $box = imagettfbbox($fontSize, 0, $font, $text);
            if (($box[2] - $box[0]) <= $maxWidth) {
                return $text;
            }
            $text = rtrim(mb_substr($text, 0, -1));
        }

        return $text;
    }
}
