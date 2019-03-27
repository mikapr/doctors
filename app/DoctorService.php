<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorService extends Model
{
    //

    public function getPriceAttribute($price)
    {
        return $price / 100;
    }

    public function setPriceAttribute($price)
    {
        return $this->attributes['price'] = $price * 100;
    }

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'id', 'doctor_id');
    }
}
