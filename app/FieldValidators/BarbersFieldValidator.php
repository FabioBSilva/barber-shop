<?php

namespace App\FieldValidators;

class BarbersFieldValidator
{

    public static function store()
    {
        $rules = [
            'name'     => 'required|max:255',
            'street'   => 'required|max:255',
            'district' => 'required|max:255',
            'number'   => 'required|integer|max:255',
            'city'     => 'required|max:255',
            'zip'      => 'required|min:8'
        ];

        return $rules;
    }

    public static function storeHairdresser()
    {
        $rules = [
            'name' => 'required|max:255'
        ];

        return $rules;
    }

}