<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorAppointment extends Model
{
    protected $fillable = [
        'day',
        'doctor_id',
        'slot',
        'user_id',
        'service_id'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function doctor()
    {
        return $this->belongsTo(Service::class, 'doctor_id', 'id');
    }
}
