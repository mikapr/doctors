<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'name'
    ];

    public function services()
    {
        return $this->hasMany(DoctorService::class, 'doctor_id');
    }
}
