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

        $employee = Employee::where('employee_code',$request->employee_code)->first();

        if(!$employee){
            return back()->with('error','Invalid Employee Code or Password');
        }

        // password = employee_code
        if($request->password != $employee->employee_code || $employee->designation_name != 'BE'){
            return back()->with('error','Invalid Employee Code or Password');
        }

        // LOGIN USER
        Auth::guard('employee')->login($employee);

        return redirect()->route('dashboard');

    }

    public function dashboard()
    {

        $employee_id = Auth::guard('employee')->user()->id;

        $doctor_count = Doctor::where('employee_id',$employee_id)->whereNotNull('speciality')->count();

        return view('employee.dashboard',compact('doctor_count'));
    }

    public function logout()
    {
        Auth::guard('employee')->logout();

        return redirect()->route('login');
    }

}
