<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Doctor;
use Auth;

class EmployeeLoginController extends Controller
{

    public function loginForm()
    {
        return view('employee.login');
    }

    public function login(Request $request)
    {
        $employeeCode = trim((string) $request->employee_code);

        $employee = Employee::where('employee_code', $employeeCode)->first();

        if(!$employee){
            return back()->with('error','Invalid Employee Code or Password');
        }

        // password = employee_code
        // 'BE' / 'BE - ...' ya 'TBM' / 'TBM - ...' wale sabhi employees login kar sakte hain
        $designation = strtoupper(trim((string) $employee->designation_name));
        $isAuthorized = (bool) preg_match('/^(BE|TBM)(\s*[-–—]|\s|$)/i', $designation);

        if ($request->password != $employee->employee_code || !$isAuthorized) {
            return back()->with('error', 'Invalid Employee Code or Password');
        }

        // LOGIN USER
        Auth::guard('employee')->login($employee);

        return redirect()->route('dashboard');

    }

    public function dashboard()
    {
        $employee = Auth::guard('employee')->user();
        $designation = strtoupper(trim((string) ($employee->designation_name ?? '')));
        $isAuthorized = (bool) preg_match('/^(BE|TBM)(\s*[-–—]|\s|$)/i', $designation);

        if (!$isAuthorized) {
            Auth::guard('employee')->logout();
            return redirect()->route('login')->with('error', 'Invalid Employee Code or Password');
        }

        $employee_id = $employee->id;

        $doctor_count = Doctor::where('employee_id',$employee_id)->whereNotNull('speciality')->count();

        return view('employee.dashboard',compact('doctor_count'));
    }

    public function logout()
    {
        Auth::guard('employee')->logout();

        return redirect()->route('login');
    }

}
