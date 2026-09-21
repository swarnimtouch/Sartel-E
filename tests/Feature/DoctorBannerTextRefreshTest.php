<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Services\DoctorBannerService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DoctorBannerTextRefreshTest extends TestCase
{
    public function test_existing_banner_text_can_be_refreshed_without_ai_generation(): void
    {
        Storage::fake('s3');

        $path = 'employee_test/banners/doctor.png';
        $original = file_get_contents(public_path('images/doctor-banners/male-template.jpg'));
        Storage::disk('s3')->put($path, $original);

        $doctor = new Doctor([
            'doctor_name' => 'Rahul Sharma',
            'speciality' => 'Cardiology',
            'gender' => 'Male',
            'banner_path' => $path,
        ]);

        $refreshed = app(DoctorBannerService::class)->refreshBannerText($doctor);

        $this->assertTrue($refreshed);
        Storage::disk('s3')->assertExists($path);

        $updated = Storage::disk('s3')->get($path);
        $this->assertNotSame(md5($original), md5($updated));

        $size = getimagesizefromstring($updated);
        $this->assertSame(2550, $size[0]);
        $this->assertSame(3300, $size[1]);
    }
}
