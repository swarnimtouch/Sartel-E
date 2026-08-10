<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

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
            ->whereNotNull('speciality')
            ->where('speciality', '!=', '')
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

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                '#',
                'Doctor Name',
                'MSL Code',
                'Language',
                'Gender',
                'Employee Name',
                'Employee Code',
                'Speciality',
                'Hospital Name',
                'Birth Date',
                'Updated Date',
                'Photo URL',
                'Banner URL',
            ]);

            foreach ($doctors as $i => $doc) {
                $photoUrl = $doc->photo
                    ? 'https://swarnimpolling.s3.ap-south-1.amazonaws.com/' . $doc->photo
                    : '';

                fputcsv($handle, [
                    $i + 1,
                    $doc->doctor_name             ?? '',
                    $doc->msl_code                ?? '',
                    $doc->language                ?? '',
                    $doc->gender                  ?? '',
                    $doc->employee->name          ?? '',
                    $doc->employee->employee_code ?? '',
                    $doc->speciality              ?? '',
                    $doc->hospital_name           ?? '',
                    $doc->birth_date              ?? '',
                    optional($doc->updated_at)->format('d M Y') ?? '',
                    $photoUrl,
                    $doc->banner_path
                        ? 'https://swarnimpolling.s3.ap-south-1.amazonaws.com/' . $doc->banner_path
                        : '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function downloadPhotos(Request $request)
    {
        // A large export can legitimately take several minutes. More importantly,
        // keep every S3 object out of PHP/ZipArchive memory by staging it on disk.
        set_time_limit(0);

        $query = Doctor::with('employee')
            ->where(function ($q) {
                $q->where(function ($photo) {
                    $photo->whereNotNull('photo')->where('photo', '!=', '');
                })->orWhere(function ($banner) {
                    $banner->whereNotNull('banner_path')->where('banner_path', '!=', '');
                });
            })
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

        if (!(clone $query)->exists()) {
            return back()->with('error', 'Koi photo ya banner available nahi hai.');
        }

        $zipFileName = 'doctors_photos_and_banners_' . now()->format('Ymd_His') . '.zip';
        $exportId = (string) \Illuminate\Support\Str::uuid();
        $exportDirectory = storage_path('app/temp/photo-exports/' . $exportId);
        $zipFilePath = $exportDirectory . DIRECTORY_SEPARATOR . $zipFileName;
        $stagingDirectory = $exportDirectory . DIRECTORY_SEPARATOR . 'staging';

        File::ensureDirectoryExists($stagingDirectory);

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            File::deleteDirectory($exportDirectory);
            return back()->with('error', 'Zip file create nahi ho payi.');
        }

        $added = 0;
        $skipped = 0;
        $zipIsOpen = true;

        try {
            $query->orderBy('id')->chunkById(100, function ($doctors) use (
                $zip,
                $stagingDirectory,
                &$added,
                &$skipped
            ) {
                foreach ($doctors as $doctor) {
                    $employeeName = $doctor->employee
                        ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $doctor->employee->name ?? 'unknown')
                        : 'unknown';
                    $employeeCode = preg_replace(
                        '/[^a-zA-Z0-9_-]/',
                        '_',
                        $doctor->employee->employee_code ?? 'emp_' . ($doctor->employee_id ?? '0')
                    );
                    $folderName = $employeeCode . '_' . $employeeName;
                    $doctorSlug = preg_replace('/\s+/', '_', strtolower(trim($doctor->doctor_name ?? 'doctor')));
                    $doctorSlug = preg_replace('/[^a-z0-9_-]/', '', $doctorSlug) ?: 'doctor';

                    $assets = [
                        'photos' => $doctor->photo,
                        'banners' => $doctor->banner_path,
                    ];

                    foreach ($assets as $assetFolder => $s3Path) {
                        if (!$s3Path) {
                            continue;
                        }

                        $source = null;
                        $destination = null;
                        $stagedPath = $stagingDirectory . DIRECTORY_SEPARATOR . $doctor->id . '_' . $assetFolder;

                        try {
                            $source = Storage::disk('s3')->readStream($s3Path);
                            $destination = fopen($stagedPath, 'wb');

                            if (!is_resource($source) || $destination === false) {
                                throw new \RuntimeException('S3 stream open nahi ho paya.');
                            }

                            if (stream_copy_to_stream($source, $destination) === false) {
                                throw new \RuntimeException('S3 stream copy nahi ho paya.');
                            }

                            $extension = strtolower(pathinfo($s3Path, PATHINFO_EXTENSION));
                            $extension = preg_match('/^[a-z0-9]{1,10}$/', $extension) ? $extension : 'png';
                            $suffix = $assetFolder === 'banners' ? '_banner' : '_photo';
                            $fileName = $doctorSlug . '_' . $doctor->id . $suffix . '.' . $extension;
                            $zipPath = $folderName . '/' . $assetFolder . '/' . $fileName;

                            if (!$zip->addFile($stagedPath, $zipPath)) {
                                throw new \RuntimeException('Image ZIP me add nahi ho payi.');
                            }

                            $added++;
                        } catch (\Throwable $e) {
                            $skipped++;
                            File::delete($stagedPath);
                            Log::warning('Doctor image export skipped.', [
                                'doctor_id' => $doctor->id,
                                'asset_type' => $assetFolder,
                                'path' => $s3Path,
                                'error' => $e->getMessage(),
                            ]);
                        } finally {
                            if (is_resource($source)) {
                                fclose($source);
                            }
                            if (is_resource($destination)) {
                                fclose($destination);
                            }
                        }
                    }
                }
            });

            $closed = $zip->close();
            $zipIsOpen = false;

            if (!$closed) {
                throw new \RuntimeException('ZIP finalize nahi ho payi.');
            }
        } catch (\Throwable $e) {
            if ($zipIsOpen) {
                try {
                    $zip->close();
                } catch (\Throwable) {
                    // The archive may already be invalid; cleanup below is enough.
                }
            }
            File::deleteDirectory($exportDirectory);
            Log::error('Doctor photo export failed.', ['error' => $e->getMessage()]);

            return back()->with('error', 'Photos aur banners ZIP create nahi ho payi. Please dobara try karein.');
        }

        // ZipArchive needs staged files until close(); they can now be removed.
        File::deleteDirectory($stagingDirectory);

        if ($added === 0) {
            File::deleteDirectory($exportDirectory);

            return back()->with('error', 'S3 se koi photo ya banner download nahi ho paya. Logs check karein.');
        }

        Log::info('Doctor photo and banner export completed.', compact('added', 'skipped'));

        // BinaryFileResponse removes the ZIP after sending it; remove its now-empty
        // unique parent directory when Laravel terminates the request.
        app()->terminating(fn () => File::deleteDirectory($exportDirectory));

        return response()
            ->download($zipFilePath, $zipFileName, [
                'X-Export-Images-Added' => (string) $added,
                'X-Export-Images-Skipped' => (string) $skipped,
            ])
            ->deleteFileAfterSend(true);
    }

}
