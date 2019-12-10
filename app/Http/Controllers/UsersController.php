<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    //POST method: create user
    //Route: /user
    public function store(Request $request)
    {   
        $this->validate($request, UsersFieldValidator::store());

        $token = Str::random(32);
        
        try{
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
                'token' => $token
            ]);

            Mail::to($user->email)->send(new Welcome($user, $token));

            return response()->json(['message' => 'success', 'user' => $user], 200);
        } catch(\Exception $e){
            return response()->json(['message' => 'error', 'error' => $e->getMessage()],500);
        }
    }

    public function login(Request $request)
    {
        $this->validate($request, UsersFieldValidator::login());

        if(Auth>>attempt(['email' => $request['email'], 'password'=>$request['password']])){
            $user = Auth::user();

            $acessToken = $user->createToken('Token')->accessToken;
            $user->access_token = $accessToken;
            return response()->json(['user'=>$user],200);
        } else{
            return response()->json(['message'=>'error', 'error'=> 'Couldn\'t find user'], 404);
        }
    }

    public function showAll()
    {
        $user = Auth::user();
        return response()->json(['message'=>'success', 'user' => $user], 200);
    }

    public function showSpecific($idUser)
    {
        $user = User::find($idUser);
        return response()->json(['user'=>$user],200);
    }

    public function scheduleUser()
    {
        $user = Auth::user();

        $schedule = $user->schedule;

        return response()->json(['message'=>'success', 'schedule'=>$schedule], 200);
    }

    public function updateUser()
    {
        $user = Auth::user();

        $user = $user->update($request->only([
            'name',
            'email',
            'password'
        ]));
    }

    public function delete()
    {
        $user = Auth::user();

        $user->delete();

        return response()->json(['success'=>'User deleted with success'],200);
    }
}
