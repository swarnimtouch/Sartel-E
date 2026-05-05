<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalDoctors = Doctor::whereNotNull('speciality')->count();
        $specialities    = Doctor::distinct('speciality')->count('speciality');
        $recentDoctors   = Doctor::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalDoctors', 'specialities',  'recentDoctors'
        ));
    }

    public function index(Request $request)
    {
        $query = Doctor::with('employee')
            ->whereNotNull('speciality'); // only updated doctors

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('doctor_name', 'like', "%$s%")
                    ->orWhere('speciality', 'like', "%$s%")
                    ->orWhere('hospital_name', 'like', "%$s%")
                    ->orWhereHas('employee', function ($emp) use ($s) {
                        $emp->where('employee_code', 'like', "%$s%");
                    });

            });
        }

        if ($request->filled('speciality')) {
            $query->where('speciality', $request->speciality);
        }

        $doctors = $query->latest()->paginate(100)->withQueryString();

        $specialities = Doctor::whereNotNull('speciality')
            ->distinct()
            ->pluck('speciality')
            ->filter()
            ->sort();

        return view('admin.doctors.index', compact('doctors','specialities'));
    }
    public function export(Request $request)
    {
        $query = \App\Models\Doctor::with('employee')
            ->whereNotNull('speciality')          // sirf jinka speciality ho
            ->where('speciality', '!=', '')       // empty string bhi skip
            ->when($request->search, function ($q) use ($request) {
                $q->where('doctor_name', 'like', '%' . $request->search . '%')
                    ->orWhere('speciality',   'like', '%' . $request->search . '%');
            });

        $doctors = $query->orderBy('created_at', 'desc')->get();

        $filename = 'doctors_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($doctors) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM – Hindi/special chars Excel mein sahi dikhenge
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($handle, [
                '#',
                'Doctor Name',
                'MSL Code',
                'Language',
                'Employee Name',
                'Employee Code',
                'Speciality',
                'Hospital Name',
                'Birth Date',
                'Updated Date',
                'Photo URL',
            ]);

            // Data rows
            foreach ($doctors as $i => $doc) {
                $photoUrl = $doc->photo
                    ? 'https://swarnimpolling.s3.ap-south-1.amazonaws.com/' . $doc->photo
                    : '';

                fputcsv($handle, [
                    $i + 1,
                    $doc->doctor_name             ?? '',
                    $doc->msl_code                ?? '',
                    $doc->language                ?? '',
                    $doc->employee->name          ?? '',
                    $doc->employee->employee_code ?? '',
                    $doc->speciality              ?? '',
                    $doc->hospital_name           ?? '',
                    $doc->birth_date              ?? '',
                    optional($doc->updated_at)->format('d M Y') ?? '',
                    $photoUrl,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function downloadPhotos(Request $request)
    {
        // Filters apply (same as index page)
        $query = Doctor::with('employee')
            ->whereNotNull('photo')
            ->where('photo', '!=', '')
            ->whereNotNull('speciality');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('doctor_name', 'like', "%$s%")
                    ->orWhere('speciality', 'like', "%$s%")
                    ->orWhere('hospital_name', 'like', "%$s%")
                    ->orWhereHas('employee', function ($emp) use ($s) {
                        $emp->where('employee_code', 'like', "%$s%");
                    });

            });
        }

        if ($request->filled('speciality')) {
            $query->where('speciality', $request->speciality);
        }

        $doctors = $query->get();

        if ($doctors->isEmpty()) {
            return back()->with('error', 'Koi photo available nahi hai.');
        }

        // Temp zip file
        $zipFileName = 'doctors_photos_' . now()->format('Ymd_His') . '.zip';
        $zipFilePath = storage_path('app/temp/' . $zipFileName);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0777, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Zip file create nahi ho payi.');
        }

        foreach ($doctors as $doctor) {
            try {
                // S3 se file content lao
                $fileContent = \Illuminate\Support\Facades\Storage::disk('s3')->get($doctor->photo);

                if (!$fileContent) continue;

                // Employee folder name
                $employeeName = $doctor->employee
                    ? preg_replace('/[^a-zA-Z0-9_]/', '_', $doctor->employee->name ?? 'unknown')
                    : 'unknown';
                $employeeCode = $doctor->employee->employee_code ?? 'emp_' . ($doctor->employee_id ?? '0');

                $folderName = $employeeCode . '_' . $employeeName;

                // Doctor image filename
                $doctorSlug = preg_replace('/\s+/', '_', strtolower(trim($doctor->doctor_name ?? 'doctor')));
                $doctorSlug = preg_replace('/[^a-z0-9_]/', '', $doctorSlug);
                $ext        = pathinfo($doctor->photo, PATHINFO_EXTENSION) ?: 'png';
                $fileName   = $doctorSlug . '_' . $doctor->id . '.' . $ext;

                // Zip mein add karo: EmployeeFolder/doctor_image.png
                $zip->addFromString($folderName . '/' . $fileName, $fileContent);

            } catch (\Exception $e) {
                // Agar ek file fail ho toh skip karo, baaki continue
                continue;
            }
        }

        $zip->close();

        // Download karke temp file delete karo
        return response()->download($zipFilePath, $zipFileName)->deleteFileAfterSend(true);
    }

}
