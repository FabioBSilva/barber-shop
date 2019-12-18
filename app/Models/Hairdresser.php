<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hairdresser extends Model
{
    protected $fillable = [
        'name', 'barber_id'
    ];

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function hairdresser()
    {
        return $this->belongsTo(User::class);
    }
}
