<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    //
    protected $fillable = [
        'employee_id',
        'doctor_name',
        'photo',
        'hospital_name',
        'speciality',
        'msl_code',
        'birth_date',
        'language',
        'gender',
        'banner_path',
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
