<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'date','barber_id','id'
    ];

    public function user()
    {
        return $this->hasOne();
    }

    public function barbers()
    {
        return $this->belongsToMany();
    }
}
