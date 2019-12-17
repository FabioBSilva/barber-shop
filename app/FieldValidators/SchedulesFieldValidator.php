<?php

namespace App\FieldValidators;

class SchedulesFieldValidator
{
    public static function storeSchedule()
    {
        $rules = [
            'hour' => 'required|date_format:H:i'
        ];

        return $rules;
    }

    public static function updateSchedule()
    {
        $rules = [
            'hour' => 'nullable|date_format:H:i'
        ];

        return $rules;
    }
}