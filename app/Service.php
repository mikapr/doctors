<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name'
    ];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class,'doctor_services', 'service_id', 'doctor_id', 'id');
    }
}
