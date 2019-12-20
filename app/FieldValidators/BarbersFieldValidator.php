<?php

namespace App\FieldValidators;

class BarbersFieldValidator
{

    public static function store()
    {
        $rules = [
            'name'     => 'required|string',
            'street'   => 'required|string',
            'district' => 'required|string',
            'number'   => 'required|string',
            'city'     => 'required|string',
            'zip'      => 'required|string|min:8',
            'state'    => 'required|string',
            'logo'     => 'nullable'
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

    public static function updateBarber()
    {
        $rules = [
            'name'      => 'nullable|string',
            'street'    => 'nullable|string',
            'district'  => 'nullable|string',
            'number'    => 'nullable|string',
            'city'      => 'nullable|string',
            'zip'       => 'nullable|min:8',
            'state'     => 'nullable|string',
            'logo'      => 'nullable'
        ];

        return $rules;
    }

    public static function updateHairdresser()
    {
        $rules = [
            'name' => 'nullable|max:255'
        ];

        return $rules;
    }

}