<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarbersController extends Controller
{
    public function store()
    {
        $this->validate($request, BarbersFieldValidator::store());

        
    }
}
