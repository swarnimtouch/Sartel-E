<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\DoctorBannerService;
use Illuminate\Support\Str;

class DoctorController extends Controller
{

    public function index()
    {
        $employee_id = Auth::id();

        $doctors = Doctor::where('employee_id', $employee_id)
            ->whereNotNull('speciality')
            ->paginate(10);

        return view('doctor.index', compact('doctors'));
    }

    public function create()
    {
        $employees = Employee::orderBy('name')->get();
        return view('doctor.create', compact('employees'));
    }

    public function store(Request $request, DoctorBannerService $bannerService)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'birth_date' => 'required|date',
            'speciality' => 'required',
            'language' => 'required',
            'gender' => 'required|in:Male,Female',
            'hospital_name' => 'required',
            'cropped_image' => 'required'
        ]);

        $employee = Employee::findOrFail(Auth::id());
        $employee_id = $employee->id;

        $employee_code = $employee->employee_code ?? 'emp_' . $employee_id;
        $employee_name = Str::slug($employee->name ?: 'employee', '_');

        $doctor = Doctor::findOrFail($request->doctor_id);

        $doctorSlug = strtolower(trim($doctor->doctor_name));
        $doctorSlug = preg_replace('/\s+/', '_', $doctorSlug);
        $doctorSlug = preg_replace('/[^a-z0-9_]/', '', $doctorSlug);

        $imageName = $doctorSlug . '_' . time() . '.png';

        $croppedImage = $request->cropped_image;

        if (str_contains($croppedImage, ';base64,')) {
            $croppedImage = substr($croppedImage, strpos($croppedImage, ';base64,') + 8);
        }

        $croppedImage = str_replace(' ', '+', $croppedImage);

        $imageData = base64_decode($croppedImage, true);

        if (!$imageData) {
            return back()->withErrors(['cropped_image' => 'Image processing failed. Please crop again.']);
        }

        $s3Path = "employee_{$employee_code}_{$employee_name}/{$imageName}";

        Storage::disk('s3')->put($s3Path, $imageData, [
            'visibility' => 'public',
            'ContentType' => 'image/png',
        ]);

        $doctor->update([
            'employee_id' => $employee_id,
            'speciality' => $request->speciality,
            'hospital_name' => $request->hospital_name,
            'birth_date' => $request->birth_date,
            'language' => $request->language,
            'gender' => $request->gender,
            'photo' => $s3Path,
        ]);

        $bannerService->generate($doctor->fresh('employee'));

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor added successfully!');
    }


    public function edit($id)
    {
        $doctor = Doctor::where('id', $id)
            ->where('employee_id', Auth::id())
            ->firstOrFail();

        return view('doctor.edit', compact('doctor'));
    }

    public function update(Request $request, $id, DoctorBannerService $bannerService)
    {
        $request->validate([
            'gender' => 'required|in:Male,Female',
        ]);

        $employee = Employee::findOrFail(Auth::id());
        $employee_id = $employee->id;
        $employee_code = $employee->employee_code ?? 'emp_' . $employee_id;
        $employee_name = Str::slug($employee->name ?: 'employee', '_');

        $doctor = Doctor::where('id', $id)
            ->where('employee_id', $employee_id)
            ->firstOrFail();

        $data = [
            'doctor_name' => $request->doctor_name,
            'speciality' => $request->speciality,
            'hospital_name' => $request->hospital_name,
            'birth_date' => $request->birth_date,
            'city' => $request->city,
            'mobile' => $request->mobile,
            'language' => $request->language,
            'gender' => $request->gender,
        ];

        if ($request->filled('cropped_image')) {

            // Delete old image from S3
            if ($doctor->photo) {
                Storage::disk('s3')->delete($doctor->photo);
            }

            $doctorSlug = strtolower(trim($doctor->doctor_name));
            $doctorSlug = preg_replace('/\s+/', '_', $doctorSlug);
            $doctorSlug = preg_replace('/[^a-z0-9_]/', '', $doctorSlug);

            $imageName = $doctorSlug . '_' . time() . '.png';

            // ✅ Base64 properly clean karo
            $croppedImage = $request->cropped_image;

            if (str_contains($croppedImage, ';base64,')) {
                $croppedImage = substr($croppedImage, strpos($croppedImage, ';base64,') + 8);
            }

            $croppedImage = str_replace(' ', '+', $croppedImage);
            $imageData = base64_decode($croppedImage, true);

            if (!$imageData) {
                return back()->withErrors(['cropped_image' => 'Image processing failed. Please crop again.']);
            }

            // ✅ Abhi employee_id use ho raha hai, baad mein employee_code se replace kar lena
            $s3Path = "employee_{$employee_code}_{$employee_name}/{$imageName}";

            Storage::disk('s3')->put($s3Path, $imageData, [
                'visibility' => 'public',
                'ContentType' => 'image/png',
            ]);

            $data['photo'] = $s3Path;
        }

        $doctor->update($data);

        $bannerService->generate($doctor->fresh('employee'));

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor updated successfully!');
    }

    public function destroy(Doctor $doctor)
    {
        if ($doctor->photo && file_exists(public_path($doctor->photo))) {
            unlink(public_path($doctor->photo));
        }

        $doctor->delete();

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor deleted successfully!');
    }

    public function doctorsByEmployee(Request $request)
    {
        $doctors = Doctor::whereNull('speciality')->where('employee_id', Auth::id())
            ->get(['id', 'doctor_name', 'msl_code']);

        return response()->json($doctors);
    }

    public function getMslNumber(Request $request)
    {
        $doctor = Doctor::find($request->doctor_id);
        return response()->json(['msl_code' => $doctor?->msl_code ?? '']);
    }
}
