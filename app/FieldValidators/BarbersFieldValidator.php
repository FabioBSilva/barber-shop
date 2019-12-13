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

    public static function updateBarber()
    {
        $rules = [
            'name'      => 'nullable|max:255',
            'street'    => 'nullable|max:255',
            'district'  => 'nullable|max:255',
            'number'    => 'nullable|integer|max:255',
            'city'      => 'nullable|max:255',
            'zip'       => 'nullable|min:8',
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