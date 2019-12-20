<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    protected $fillable = [
        'name','street','district','number','city','zip','state','logo','user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hairdresser()
    {
        return $this->hasMany(Hairdresser::class);
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class);
    }
}
