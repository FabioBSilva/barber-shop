<?php

namespace App\FieldValidators;

class UsersFieldValidator
{
    public static function store(){

        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ];
    }

    public static function login() {

        $rules = [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ];
    }

    public static function update()
    {
        $rules = [
            'name' => 'max:255',
            'email' => 'email|unique:users,email',
            'password' => 'min:8'
        ];
    }
}