<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'time', 'date'
    ];

    public function user()
    {
        return $this->belongsTo();
    }

    public function barber()
    {
        return $this->belongsToMany();
    }
}
