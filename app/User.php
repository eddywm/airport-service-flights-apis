<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'name', 'email'
    ];

    public function flights(){
        return $this->belongsToMany('App\Flight', 'flight_user');
    }

    public function save(array $options = [])
    {
        if(empty($this->api_token)){
            $this->api_token = str_random(60);
        }
        return parent::save($options);
    }


}
