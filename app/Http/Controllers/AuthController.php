<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Imports\DoctorImport;
use Maatwebsite\Excel\Facades\Excel;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('admin.dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Email or password Wrong.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function importPage()
    {
        return view('admin.doctors.import');
    }

    public function importDoctors(Request $request)
    {
        $import = new DoctorImport;

        Excel::import($import, $request->file('file'));

        $inserted = $import->getInsertedCount();
        $updated = $import->getUpdatedCount();

        return back()->with('success', "Import Complete: {$inserted} inserted, {$updated} updated.");
    }


}
