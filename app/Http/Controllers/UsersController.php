<?php

namespace App\Http\Controllers;

use App\User;
use DateInterval;
use App\Mail\Welcome;
use App\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\FieldValidators\UsersFieldValidator;
use Laravel\Passport\Bridge\PersonalAccessGrant;

class UsersController extends Controller
{
    //POST method: create user
    //Route: /user
    public function store(Request $request)
    {   
        $this->validate($request, UsersFieldValidator::store());

        $token = Str::random(32);
        
        try{
            DB::beginTransaction();

            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
                'barber' => $request['barber'],
                'avatar' => $request['avatar'],
                'token' => $token
            ]);

            //Mail::to($user->email)->send(new Welcome($user, $token));
            $user['token'] = $this->setUserToken($user)->accessToken;
            DB::commit();

            return response()->json(['message' => 'success', 'user' => $user], 200);
        } catch(\Throwable $th){
            DB::rollback();
            return response()->json(['message' => 'error', 'exception' => $th->getMessage()],500);
        }
    }

    public function login(Request $request)
    {
        $this->validate($request, UsersFieldValidator::login());

        if (!Auth::attempt(['email' => $request['email'], 'password'=>$request['password']]))
            return response(['message' => 'Unauthorized'], 401);

        $user = $request->user();
        $token = $user->createToken('access-token');
        return response([
            'message' => 'Successfuly logged',
            'user' => $user,
            'access_token' => $token->accessToken,
            'expires_at' => $token->token->expires_at
        ], 200); 
    }

    function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response(['message' => 'Successfully logout'], 200);
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

    public function update(Request $request)
    {
        $this->validate($request, UsersFieldValidator::update());
        $user = Auth::user();

        try{
           $user->update([
                'name' => $request->input('name', $user->name),
                'email'=> $request->input('email', $user->email),
            ]);

            $user['avatar'] = $user->avatar;
            if($request->hasFile('avatar') && $request->file('avatar')->isValid()){
                if($user->avatar)
                $name = $user->avatar;
                else
                    $name = $user->id.Str::kebab($user->name);
                $extension = $request->avatar->extension();
                $nameFile = "{$name}.{$extension}";

                $user->update([
                    'avatar' => $nameFile
                    ]);

                $upload = $request->avatar->storeAs('users', $nameFile);

                if(!$upload) return response()->json(['Erro ao fazer upload da imagem']);
                
            }

            return response()->json(['message'=>'success', 'user' => $user], 200);
        }catch(\Throwable $th){
            return response()->json(['message' => 'error', 'exception' => $th->getMessage()],500);
        }
    }

    public function changePassword(Request $request)
    {
        $this->validate($request, UsersFieldValidator::changePassword());
        $user = Auth::user();

        $oldPassword = $user->password;

        if (Hash::check($request->password, $oldPassword)) {

            if ($request->password != $request->new_password) {

                $user->update(['password' => bcrypt($request->new_password)]);

                //Mail::to($user->email)->send(new NewPassword($user));

                return response()->json(['message' => 'success', 'success' => 'Password has been changed'], 200);
            } else {
                return response()->json(['message' => 'error', 'error' => 'Password are equals'], 422);
            }
        } else {
            return response()->json(['message' => 'error', 'error' => 'Password entered does not match current password'], 400);
        }
    }

    public function recoverEmail(Request $request)
    {
        $this->validate($request, UsersFieldValidator::recoverEmail());

        $token = Str::random(32);

        try {
            $user = User::where('email', $request->email)->first();

            if (!isset($user)) {
                return response()->json(['message' => 'error', 'error' => 'This email does not belong to any users'], 404);
            }

            PasswordReset::create([
                'email' => $request->email,
                'token' => $token
            ]);

            //Mail::to($user->email)->send(new PasswordRecovery($user, $token));

            return response()->json([
                'message' => 'success',
                'observation' => 'Enviamos um email para ' . $user->email . ' para redefinição de senha.',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $this->validate($request, UsersFieldValidator::resetPassword());

        try {
            //get the token in the PasswordReset table where they match
            $passwordReset = PasswordReset::where('token', $request->query('token'))->first();
            if (!isset($passwordReset)) {
                return response()->json(['error' => 'Invalid token'], 400);
            }
            //change password
            User::where('email', $passwordReset->email)->update([
                'password' => bcrypt($request->password)
            ]);

            //delete token
            $passwordReset->delete();

            return response()->json(['success' => 'Password redefined'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete()
    {
        $user = Auth::user();
        $user->delete();
        return response()->json(['success'=>'User deleted with success'],200);
    }

    private function setUserToken(User $user){
        $authorizarionServer = app()->make(\League\OAuth2\Server\AuthorizationServer::class);
        $authorizarionServer->enableGrantType(
            new PersonalAccessGrant, new DateInterval('PT12H')
        );
        $token = $user->createToken('AccessToken');
        return $token;
    }
}
