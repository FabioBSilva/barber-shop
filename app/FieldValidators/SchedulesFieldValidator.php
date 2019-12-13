<?php

namespace App\FieldValidators;

class SchedulesFieldValidator
{
    public static function storeSchedule()
    {
        $rules = [
            'date' => 'required|date_format:Y-m-d H:i:s'
        ];

        return $rules;
    }

    public static function updateSchedule()
    {
        $rules = [
            'date' => 'nullable|date_format:Y-m-d H:i:s'
        ];

        return $rules;
    }
}