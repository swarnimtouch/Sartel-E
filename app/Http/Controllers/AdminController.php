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
                    ->orWhere('hospital_name', 'like', "%$s%");
            });
        }

        if ($request->filled('speciality')) {
            $query->where('speciality', $request->speciality);
        }

        $doctors = $query->latest()->paginate(10)->withQueryString();

        $specialities = Doctor::whereNotNull('speciality')
            ->distinct()
            ->pluck('speciality')
            ->filter()
            ->sort();

        return view('admin.doctors.index', compact('doctors','specialities'));
    }
}
