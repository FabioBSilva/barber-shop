<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'date','barber_id'
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
