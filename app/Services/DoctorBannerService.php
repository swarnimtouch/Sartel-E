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
            $template = $this->preparePrintBanner($template);

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

    public function generateAiBanner(Doctor $doctor): ?string
    {
        $templateName = match ($doctor->gender) {
            'Male' => 'male-template.jpg',
            'Female' => 'female-template.jpg',
            default => null,
        };

        if (!$templateName) {
            throw new RuntimeException('Doctor gender must be Male or Female to generate banner.');
        }

        if (!$doctor->photo || !Storage::disk('s3')->exists($doctor->photo)) {
            throw new RuntimeException('Doctor photo not found on S3. Please upload photo first.');
        }

        $apiKey = config('services.openai.api_key', env('OPENAI_API_KEY'));
        $baseUrl = config('services.openai.base_url', env('OPENAI_BASE_URL', 'https://api.openai.com/v1'));
        $model = config('services.openai.image_model', env('OPENAI_IMAGE_MODEL', 'gpt-image-2'));
        $quality = config('services.openai.image_quality', 'low');
        $size = config('services.openai.image_size', '1024x1328');
        $timeout = (int) config('services.openai.timeout', 180);

        if (!$apiKey) {
            throw new RuntimeException('OpenAI API key is missing in configuration.');
        }

        try {
            // Load template and resize down for API efficiency (~1024x1325)
            $templateFullPath = public_path('images/doctor-banners/' . $templateName);
            if (!file_exists($templateFullPath)) {
                throw new RuntimeException("Template file {$templateName} does not exist.");
            }

            $templateResizedBytes = $this->getResizedTemplateBytes($templateFullPath, 1024);
            $doctorPhotoBytes = Storage::disk('s3')->get($doctor->photo);

            $genderText = strtolower($doctor->gender);
            $prompt = "Photo 1 is a medical poster featuring a {$genderText} doctor in a white coat and stethoscope. Photo 2 is a reference photo of the real doctor.

Replace the head and face of the doctor in Photo 1 with the head and face of the doctor in Photo 2:
1. Transfer the doctor's head, face, cap/hat/headwear, hair, spectacles/glasses, and facial features from Photo 2 EXACTLY as they appear in Photo 2. Do NOT remove or modify any cap, hat, turban, headwear, hair, or glasses that the person in Photo 2 is wearing. Preserve the exact original appearance, headwear, and authentic features from Photo 2.
2. Seamlessly blend the neck and head of the doctor from Photo 2 onto the white coat, collar, and body of the doctor in Photo 1.
3. Keep the doctor's white coat, stethoscope, pointing pose, background colors (green gradient, white, dark blue), text 'Get to Goal 130/80', graphics, and all other elements of Photo 1 100% intact and unchanged.";

            Log::info("Requesting OpenAI AI face-swap banner for Doctor ID: {$doctor->id} ({$doctor->doctor_name}) using model {$model}");

            $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
                ->timeout($timeout)
                ->attach('image[]', $templateResizedBytes, 'banner.png', ['Content-Type' => 'image/png'])
                ->attach('image[]', $doctorPhotoBytes, 'doctor.png', ['Content-Type' => 'image/png'])
                ->post("{$baseUrl}/images/edits", [
                    'model' => $model,
                    'prompt' => $prompt,
                    'quality' => $quality,
                    'size' => $size,
                ]);

            if (!$response->successful()) {
                $errorMsg = $response->json('error.message') ?? $response->body();
                throw new RuntimeException("OpenAI API error ({$response->status()}): {$errorMsg}");
            }

            $responseData = $response->json('data.0');
            $aiImageBytes = null;

            if (!empty($responseData['b64_json'])) {
                $aiImageBytes = base64_decode($responseData['b64_json']);
            } elseif (!empty($responseData['url'])) {
                $aiImageBytes = file_get_contents($responseData['url']);
            }

            if (!$aiImageBytes) {
                throw new RuntimeException('No image data returned from OpenAI.');
            }

            $aiImage = imagecreatefromstring($aiImageBytes);
            if (!$aiImage) {
                throw new RuntimeException('Failed to parse AI generated image from OpenAI.');
            }

            // Work at the source template size, then crop to the final 8.4 x 11 inch print canvas.
            $finalBanner = imagecreatetruecolor(2550, 3300);
            imagecopyresampled(
                $finalBanner,
                $aiImage,
                0, 0, 0, 0,
                2550, 3300,
                imagesx($aiImage),
                imagesy($aiImage)
            );
            imagedestroy($aiImage);

            // Overlay Doctor name and speciality in high resolution with shadow
            $this->placeText($finalBanner, $doctor);
            $finalBanner = $this->preparePrintBanner($finalBanner);

            ob_start();
            imagepng($finalBanner, null, 7);
            $bannerContents = ob_get_clean();
            imagedestroy($finalBanner);

            $employeeCode = $doctor->employee?->employee_code ?? 'emp_' . $doctor->employee_id;
            $employeeName = str($doctor->employee?->name ?: 'employee')->slug('_')->value();
            $doctorSlug = str($doctor->doctor_name)->slug('_')->value() ?: 'doctor';
            $path = "employee_{$employeeCode}_{$employeeName}/banners/{$doctorSlug}_{$doctor->id}_" . time() . '.png';

            Storage::disk('s3')->put($path, $bannerContents, [
                'visibility' => 'public',
                'ContentType' => 'image/png',
            ]);

            $oldBanner = $doctor->banner_path;
            $doctor->forceFill([
                'banner_path' => $path,
                'is_generated' => true,
            ])->saveQuietly();

            if ($oldBanner && $oldBanner !== $path) {
                Storage::disk('s3')->delete($oldBanner);
            }

            Log::info("AI Face-swap banner generated and saved successfully for Doctor ID: {$doctor->id} at {$path}");

            return $path;
        } catch (Throwable $exception) {
            Log::error('AI Doctor banner generation failed.', [
                'doctor_id' => $doctor->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function refreshBannerText(Doctor $doctor): bool
    {
        $templateName = match ($doctor->gender) {
            'Male' => 'male-template.jpg',
            'Female' => 'female-template.jpg',
            default => null,
        };

        if (!$templateName || !$doctor->banner_path) {
            return false;
        }

        $disk = Storage::disk('s3');

        if (!$disk->exists($doctor->banner_path)) {
            throw new RuntimeException('Existing doctor banner was not found on S3.');
        }

        $banner = imagecreatefromstring($disk->get($doctor->banner_path));
        $template = imagecreatefromjpeg(public_path('images/doctor-banners/' . $templateName));

        if (!$banner || !$template) {
            throw new RuntimeException('Banner or template image could not be read.');
        }

        try {
            if (imagesx($banner) !== 2550 || imagesy($banner) !== 3300) {
                throw new RuntimeException('Existing banner dimensions are not supported for text refresh.');
            }

            imagecopy(
                $banner,
                $template,
                280,
                2010,
                280,
                2010,
                1990,
                320,
            );

            $this->placeText($banner, $doctor);

            ob_start();
            imagepng($banner, null, 7);
            $bannerContents = ob_get_clean();

            if ($bannerContents === false || !$disk->put($doctor->banner_path, $bannerContents, [
                'visibility' => 'public',
                'ContentType' => 'image/png',
            ])) {
                throw new RuntimeException('Updated banner could not be saved to S3.');
            }
        } finally {
            imagedestroy($banner);
            imagedestroy($template);
        }

        return true;
    }

    private function preparePrintBanner(\GdImage $banner): \GdImage
    {
        $targetWidth = 2520;
        $targetHeight = 3300;

        if (imagesx($banner) !== $targetWidth || imagesy($banner) !== $targetHeight) {
            $cropX = max(0, (int) floor((imagesx($banner) - $targetWidth) / 2));
            $cropped = imagecrop($banner, [
                'x' => $cropX,
                'y' => 0,
                'width' => $targetWidth,
                'height' => $targetHeight,
            ]);

            if (!$cropped) {
                throw new RuntimeException('The banner could not be cropped to 8.4 x 11 inches.');
            }

            imagedestroy($banner);
            $banner = $cropped;
        }

        imageresolution($banner, 300, 300);

        return $banner;
    }

    private function getResizedTemplateBytes(string $filePath, int $targetWidth = 1024): string
    {
        list($origW, $origH) = getimagesize($filePath);
        $targetHeight = (int) round(($origH / $origW) * $targetWidth);

        $src = imagecreatefromjpeg($filePath);
        $dst = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetWidth, $targetHeight, $origW, $origH);
        imagedestroy($src);

        ob_start();
        imagepng($dst, null, 8);
        $data = ob_get_clean();
        imagedestroy($dst);

        return $data;
    }

    private function placeText(\GdImage $banner, Doctor $doctor): void
    {
        $nameFont = public_path('fonts/RobotoSlab-Bold.ttf');
        $specialityFont = public_path('fonts/RobotoSlab-Regular.ttf');
        $white = imagecolorallocate($banner, 255, 255, 255);
        $shadow = imagecolorallocatealpha($banner, 0, 0, 0, 55);
        $name = $this->fitText($this->doctorNameWithPrefix($doctor->doctor_name), $nameFont, 70, 1800);
        $speciality = $this->fitText($doctor->speciality ?: 'Doctor', $specialityFont, 55, 1800);

        imagettftext($banner, 70, 0, 363, 2123, $shadow, $nameFont, $name);
        imagettftext($banner, 70, 0, 360, 2120, $white, $nameFont, $name);
        imagettftext($banner, 55, 0, 362, 2242, $shadow, $specialityFont, $speciality);
        imagettftext($banner, 55, 0, 360, 2240, $white, $specialityFont, $speciality);
    }

    private function doctorNameWithPrefix(string $name): string
    {
        $name = trim($name);

        return preg_match('/^dr\.?\s+/i', $name) ? $name : 'Dr. ' . $name;
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
