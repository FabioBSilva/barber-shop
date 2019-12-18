<?php

namespace App\Http\Controllers;

use App\Barber;
use App\Schedule;
use App\Hairdresser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\FieldValidators\BarbersFieldValidator;

class BarbersController extends Controller
{
    private $totalPage = 5;

    //POST method: Create a barber shop
    //Route: /barber
    public function store(Request $request)
    {   
        $this->validate($request, BarbersFieldValidator::store());

        $user = Auth::user();

        $barber = $user->barber;

        if(!$barber) return response()->json(['error'=>'User is not a barber'],400);

        if($user->barber_id != null) return response()->json(['error'=>'User already has a barber shop'],400);

        try{
            DB::beginTransaction();

            $barber = Barber::create([
                'name' => $request['name'],
                'street' => $request['street'],
                'district' => $request['district'],
                'number' => $request['number'],
                'city' => $request['city'],
                'zip'   => $request['zip'],
                'user_id' => $user->id,
                'logo' => $request['logo']
            ]);

            if($request->hasFile('logo') && $request->file('logo')->isValid()){
                
                $name = $barber->id.Str::kebab($barber->name);
                $extension = $request->logo->extension();
                $nameFile = "{$name}.{$extension}";
                
                Storage::put('barbers/'.$nameFile, file_get_contents($request->file('logo')));

                $upload = Storage::url('barbers/'.$nameFile);

                $barber->update([
                    'logo' => $upload
                    ]);

            }
         
            $user->update([
                'barber_id' => $barber->id
            ]);

            DB::commit();

            return response()->json(['message' => 'success', 'barber' => $barber], 200);
        } catch (\Throwable $th){
            DB::rollBack();
            return response()->json(['error'=>$e->getMessage()], 400);
        }                 
    }

    //POST method: Create a hairdresser
    //Route: /hairdresser
    public function storeHairdresser(Request $request)
    {
        $this->validate($request, BarbersFieldValidator::storeHairdresser());

        $user = Auth::user();
        
        $barber = $user->barber;

        if(!$barber) return response()->json(['error'=>'User is not a barber'],400);
        if($user->barber_id == null) return response()->json(['error'=>'User has no registered barber shop'],400);

        try{

            $hairDresser = Hairdresser::create([
                'name' => $request['name'],
                'barber_id' => $user->barber_id
            ]);

            return response()->json(['message'=>'success', 'hair_dresser'=>$hairDresser],200);
        }catch(\Throwable $th){
            return response()->json(['error'=>$e->getMessage()],500);
        }
    }

    //GET method: Shows a specific barber shop
    //Route: /barber/{id}
    public function showBarberSpecific($idBarber)
    {
        $barber = Barber::find($idBarber);
        if($barber->hairdresser){
            $barber->hairdresser;
        }

        $schedules = Schedule::where('barber_id', $idBarber)->get();

        return response()->json(['message'=>'success','barber'=>$barber, 'schedules'=>$schedules],200);
        
        
    }

    //GET method: Show all barber shops
    //Route: /barber
    public function showBarber(Request $request)
    {
        $barbers = Barber::paginate($request->query('per_page', 5));

        return response()->json(['message'=>'success','barbers'=>$barbers], 200);
    }

    // public function sh()
    // {
    //     $user = Atuh::user();

    //     $hairDresser = 
    // }

    //GET method: Show all hairdressers that belong to barber shop
    //Route: /hairdresser
    public function showHairdresser()
    {
        $user = Auth::user();
        
        $hairDresser = Hairdresser::where('barber_id',$user->barber_id)->paginate($this->totalPage);

        return response()->json(['hairdressers'=>$hairDresser],200);
    }

    //POST method: Lets you make changes to the barber shop
    //Route: /barber/update/{id}
    public function updateBarber(Request $request, $idBarber)
    {
        $this->validate($request, BarbersFieldValidator::updateBarber());

        $barber = Barber::find($idBarber);

        try{
            $barber->update($request->only([
                'name' ,
                'street' ,
                'district' ,
                'number' ,
                'city' ,
                'zip',
            ]));

            if($request->hasFile('logo') && $request->file('logo')->isValid()){
                if($barber->logo){
                    $f = explode('storage/', $barber->logo)[1];
                    Storage::delete($f);
                }

                $name = $barber->id.Str::kebab($barber->name);
                $extension = $request->logo->extension();
                $nameFile = "{$name}.{$extension}";
                
                Storage::put('barbers/'.$nameFile, file_get_contents($request->file('logo')));

                $upload = Storage::url('barbers/'.$nameFile);

                $barber->update([
                    'logo' => $upload
                    ]);
            }

            return response()->json(['message'=>'success', 'barber'=>$barber], 200);
        }catch(\Throwable $th){
            return response()->json(['error'=>$th->getMessage()], 500);
        }
    }

    //PUT method: Lets you make hairdresser changes
    //Route: /hairdresser/{id}
    public function updateHairdresser(Request $request, $idHairdresser)
    {
        $this->validate($request, BarbersFieldValidator::updateHairdresser());
        $user = Auth::user();

        $hairDresser = Hairdresser::find($idHairdresser);

        if ($hairDresser->barber_id != $user->barber_id) return response()->json(['error'=>'Hairdresser does not currently belong to a barber shop'], 400);

        try{
            $hairDresser->update($request->only([
                'name'
            ]));

            return response()->json(['message'=>'success', 'hairdresser'=>$hairDresser], 200);
        } catch(\Throwable $th){
            return response()->json(['error'=>$e->getMessage()], 500);
        }
    }

    //DELETE method: Lets you erase the barber shop
    //Route: /barber/{id}
    public function deleteBarber($idBarber)
    {
        $user = Auth::user();
        
        $barber = Barber::find($idBarber);

        if($barber->id != $user->barber_id) return response()->json(['error'=>'Barberia does not belong to user'], 200);
        try{

            $barber->delete();

            return response()->json(['message'=>'Barber shop successfully deleted'], 200);
        }catch (\Throwable $th) {
            return response()->json(['error'=>$e->getMessage()], 500);
        }        
    }

    //DELETE method: Allows you to delete a hairdresser
    //Route: /hairdresser/{id}
    public function deleteHairdresser($idHairdresser)
    {
        $user = Auth::user();

        $hairDresser = Hairdresser::find($idHairdresser);

        if ($hairDresser->barber_id != $user->barber_id) return response()->json(['error'=>'Hairdresser does not currently belong to a barber shop'], 400);
        
        $hairDresser->delete();

        return response()->json(['message'=>'Hairdresser successfully deleted'],200);
    }   
}