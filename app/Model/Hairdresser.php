<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hairdresser extends Model
{
    protected $fillable = [
        'name'
    ];

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }
}
