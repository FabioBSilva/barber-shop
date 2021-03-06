<?php

namespace App\FieldValidators;

class UsersFieldValidator
{
    public static function store(){

        $rules = [
            'name'      => 'required|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6',
            'barber'    => 'required|boolean',
            'avatar'    => 'nullable'
        ];

        return $rules;
    }

    public static function login() {

        $rules = [
            'email'     => 'required|email|exists:users,email',
            'password'  => 'required|min:6'
        ];

        return $rules;
    }

    public static function update()
    {
        $rules = [
            'name'      => 'nullable|max:255',
            'email'     => 'nullable|email|unique:users,email',
            'password'  => 'nullable|min:6',
            'avatar'    => 'nullable'
        ];

        return $rules;
    }

    public static function changePassword()
    {
        $rules = [
            'password'     => 'required|min:6',
            'new_password' => 'required|min:6'
        ];
        
        return $rules;
    }

    public static function recoverEmail()
    {
        $rules = [
            'email' => 'email|max:255|email',
        ];

        return $rules;
    }

    public static function resetPassword()
    {
        $rules = [
            'password' => 'required|min:8',
        ];

        return $rules;
    }
}