<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'birth_date' => 'required|date',
            'speciality' => 'required',
            'hospital_name' => 'required',
            'cropped_image' => 'required'
        ]);

        $employee_id = Auth::id();
        $doctor = Doctor::findOrFail($request->doctor_id);

        $doctorSlug = strtolower(trim($doctor->doctor_name));
        $doctorSlug = preg_replace('/\s+/', '_', $doctorSlug);
        $doctorSlug = preg_replace('/[^a-z0-9_]/', '', $doctorSlug);

        $imageName = $doctorSlug.'_'.time().'.png';

        // Remove base64 prefix
        $image = preg_replace('/^data:image\/\w+;base64,/', '', $request->cropped_image);

        // Replace spaces
        $image = str_replace(' ', '+', $image);

        // Decode base64
        $imageData = base64_decode($image);

        $s3Path = "employee_{$employee_id}/{$imageName}";

        Storage::disk('s3')->put($s3Path, $imageData, [
            'visibility' => 'public',
            'ContentType' => 'image/png'
        ]);

        $doctor->update([
            'employee_id' => $employee_id,
            'speciality' => $request->speciality,
            'hospital_name' => $request->hospital_name,
            'birth_date' => $request->birth_date,
            'photo' => $s3Path,   // only path

        ]);

        return redirect()->route('doctors.index')
            ->with('success','Doctor added successfully!');
    }


    public function edit($id)
    {
        $doctor = Doctor::where('id', $id)
            ->where('employee_id', Auth::id())
            ->firstOrFail();

        return view('doctor.edit', compact('doctor'));
    }

    public function update(Request $request, $id)
    {
        $employee_id = Auth::id();

        $doctor = Doctor::where('id', $id)
            ->where('employee_id', $employee_id)
            ->firstOrFail();

        $data = [
            'speciality'    => $request->speciality,
            'hospital_name' => $request->hospital_name,
            'birth_date'    => $request->birth_date,
            'city'          => $request->city,
            'mobile'        => $request->mobile,
        ];

        if ($request->filled('cropped_image')) {

            // Delete old image
            if ($doctor->photo) {
                Storage::disk('s3')->delete($doctor->photo);
            }

            $doctorSlug = strtolower(trim($doctor->doctor_name));
            $doctorSlug = preg_replace('/\s+/', '_', $doctorSlug);
            $doctorSlug = preg_replace('/[^a-z0-9_]/', '', $doctorSlug);

            $imageName = $doctorSlug . '_' . time() . '.png';

            // Remove base64 prefix
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $request->cropped_image);

            // Replace spaces
            $image = str_replace(' ', '+', $image);

            // Decode base64
            $imageData = base64_decode($image);

            $s3Path = "employee_{$employee_id}/{$imageName}";

            Storage::disk('s3')->put($s3Path, $imageData, [
                'visibility' => 'public',
                'ContentType' => 'image/png'
            ]);

            // Save only path
            $data['photo'] = $s3Path;
        }

        $doctor->update($data);

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
        $doctors = Doctor::whereNull('speciality')
            ->get(['id', 'doctor_name', 'msl_code']);

        return response()->json($doctors);
    }

    public function getMslNumber(Request $request)
    {
        $doctor = Doctor::find($request->doctor_id);
        return response()->json(['msl_code' => $doctor?->msl_code ?? '']);
    }
}
