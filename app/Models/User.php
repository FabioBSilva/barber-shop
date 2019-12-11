<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    
   /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'name', 'password', 'email', 'avatar', 'token', 'barber', 'schedule_id','barber_id'
    ];

    protected $hidden = [
        'password', 'token'
    ];

    public function barber()
    {
        return $this->hasOne(User::class);
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class);
    }

}
