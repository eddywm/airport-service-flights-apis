<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $timestamps = false;

    public function flights() {
        return $this->belongsToMany('App\Flight', 'flight_customer');
    }
}
