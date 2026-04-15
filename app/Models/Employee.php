<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    protected $table = 'employees';

    protected $fillable = [
        'name',
        'employee_code',
        'position_code',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
}
