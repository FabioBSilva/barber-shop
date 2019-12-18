<?php

namespace App\Http\Controllers;

use File;
use App\User;
use App\Schedule;
use DateInterval;
use App\Hairdresser;
use App\Mail\Welcome;
use App\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\UserScheduleEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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

            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
                'barber' => $request['barber'],
                'token' => $token
            ]);

            if($request->hasFile('avatar') && $request->file('avatar')->isValid()){
                
                $name = $user->id.Str::kebab($user->name);
                $extension = $request->avatar->extension();
                $nameFile = "{$name}.{$extension}";
                
                Storage::put('users/'.$nameFile, file_get_contents($request->file('avatar')));

                $upload = Storage::url('users/'.$nameFile);

                $user->update([
                    'avatar' => $upload
                    ]);

            }
            Mail::to($user->email)->send(new Welcome($user));
            //$user['token'] = $this->setUserToken($user)->accessToken;
            return $this->login($request);

        } catch(\Throwable $th){
            return response()->json(['message' => 'error', 'exception' => $th->getMessage()],500);
        }
    }

    //POST method: allows user to login
    //Route: /auth
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

    //DELETE method: allows user to logout
    //Route: /auth
    function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response(['message' => 'Successfully logout'], 200);
    }

    //GET method: search all users
    //Route: /user
    public function showAll()
    {
        $user = User::all();
        return response()->json(['message'=>'success', 'user' => $user], 200);
    }

    //GET method: search for a specific user
    //Route: /user/{id}
    public function showSpecific($idUser)
    {
        $user = User::find($idUser);
        return response()->json(['user'=>$user],200);     
    }

    //POST method: allows user to make changes to their account
    //Route: /user/update
    public function update(Request $request)
    {
        $this->validate($request, UsersFieldValidator::update());
        $user = Auth::user();

        try{
           $user->update([
                'name' => $request->input('name', $user->name),
                'email'=> $request->input('email', $user->email),
            ]);

            if($request->hasFile('avatar') && $request->file('avatar')->isValid()){
                if($user->avatar){
                    $f = explode('storage/', $user->avatar)[1];
                    Storage::delete($f);
                }

                $name = $user->id.Str::kebab($user->name);
                $extension = $request->avatar->extension();
                $nameFile = "{$name}.{$extension}";
                
                Storage::put('users/'.$nameFile, file_get_contents($request->file('avatar')));

                $upload = Storage::url('users/'.$nameFile);

                $user->update([
                    'avatar' => $upload
                    ]);
            }
            return response()->json(['message'=>'success', 'user' => $user], 200);
        }catch(\Throwable $th){
            return response()->json(['message' => 'error', 'exception' => $th->getMessage()],500);
        }
    }

    //PUT method: allows the user to change their password
    //Route: /user/password
    public function changePassword(Request $request)
    {
        $this->validate($request, UsersFieldValidator::changePassword());
        $user = Auth::user();

        $oldPassword = $user->password;

        if (Hash::check($request->password, $oldPassword)) {

            if ($request->password != $request->new_password) {

                $user->update(['password' => bcrypt($request->new_password)]);

                //Mail::to($user->email)->send(new NewPassword($user));

                return response()->json(['success' => 'Password has been changed'], 200);
            } else {
                return response()->json(['message' => 'error', 'error' => 'Password are equals'], 422);
            }
        } else {
            return response()->json(['message' => 'error', 'error' => 'Password entered does not match current password'], 400);
        }
    }

    //POST method: allows user to recover their account
    //Route: /user/recover/email
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

    //PUT method: This function depends on the "recoverEmail" function.
    //            This function changes the password after the user clicks
    //            on the link sent by email
    //Route: /user/password/update
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
    
    //DELETE method: Delete user
    //Route: /user
    public function delete()
    {
        $user = Auth::user();
        $user->delete();
        return response()->json(['success'=>'User deleted with success'],200);
    }

    //POST method: Link a time to a user
    //Route: /schedule/{scheduleid}/hairdresser/{hairdresserid}
    public function storeSchedulesUser($idSchedule, $idHairdresser)
    {   
        $user = Auth::user();

        $barber = $user->barber;
        if($barber) return response()->json(['error'=>'User can\'t be a barber'],400);

        $schedule = Schedule::find($idSchedule);
        $hairdresser = Hairdresser::find($idHairdresser);

        if($user->schedule_id != null) return response()->json(['error'=>'User already has an appointment'], 400);
        if($schedule->barber_id != $hairdresser->barber_id) return response()->json(['error'=> 'Hairdresser or schedule does not belong to the same barber shop'], 400);

        $user->update([
            'schedule_id' => $schedule->id,
            'hairdresser_id' => $hairdresser->id
        ]);

        $format = [
        'user_id' => $user->id,
        'user_name'    => $user->name,
        'hairdresser_id' => $hairdresser->id,
        'hairdresser_name' => $hairdresser->name,
        'scheduled_id' => $schedule->id,
        'hour' => $schedule->hour
        ];

        event(new UserScheduleEvent($user, $schedule));

        return response()->json(['message'=>'success','user'=>$format], 200);
    }

    //DELETE method: Unlink a time to a user
    //Route: /schedule/{scheduleid}/hairdresser/{hairdresserid}
    public function deleteScheduleUser($idSchedule)
    {
        $user = Auth::user();
        $barber = $user->barber;
        if($barber) return response()->json(['error'=>'User can\'t be a barber'], 400);

        $schedule = Schedule::find($idSchedule);

        $user->update([
            'schedule_id' => null,
            'hairdresser_id' => null
        ]);

        event(new UserScheduleEvent($user, $schedule));

        return response()->json(['message'=>'Schedule successfully unlinked'], 200);
    }

    //GET method: Get the time and hairdresser that belong to the user
    //Route: /user/schedule
    public function getScheduleUser()
    {
        $user = Auth::user();

        $barber = $user->barber;
        if($barber) return response()->json(['error'=>'User can\'t be a barber'],400);

        $format = [
            'user_id' => $user->id,
            'scheduled_id' => $user->schedule_id,
            'hairdresser_id' => $user->hairdresser_id
        ];

        return response()->json(['message'=>'success', 'schedule'=>$format],200);
    }

    //Create accesstoken
    private function setUserToken(User $user){
        $authorizarionServer = app()->make(\League\OAuth2\Server\AuthorizationServer::class);
        $authorizarionServer->enableGrantType(
            new PersonalAccessGrant, new DateInterval('PT12H')
        );
        $token = $user->createToken('AccessToken');
        return $token;
    }
}
