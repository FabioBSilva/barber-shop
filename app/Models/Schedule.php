<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'hour','barber_id','id'
    ];

    public function user()
    {
        return $this->belongsTo();
    }

    public function barbers()
    {
        return $this->belongsToMany();
    }
}
