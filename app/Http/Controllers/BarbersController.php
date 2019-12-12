<?php

namespace App\Http\Controllers;

use App\Barber;
use App\Hairdresser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\FieldValidators\BarbersFieldValidator;

class BarbersController extends Controller
{
    private $totalPage = 5;

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
                'user_id' => $user->id
            ]);
         
            $user->update([
                'barber_id' => $barber->id
            ]);

            DB::commit();

            return response()->json(['message' => 'success', 'barber' => $barber], 200);
        } catch (\Excpetion $e){
            DB::rollBack();
            return response()->json(['error'=>$e->getMessage()], 400);
        }                 
    }

    public function storeHairdresser(Request $request)
    {
        $this->validate($request, BarbersFieldValidator::storeHairdresser());

        $user = Auth::user();
        
        $barber = $user->barber;

        if(!$barber) return response()->json(['error'=>'User is not a barber'],400);
        if($user->barber_id == null) return response()->json(['error'=>'User has no registered barber shop'],400);

        try{
            DB::beginTransaction();

            $hairDresser = Hairdresser::create([
                'name' => $request['name'],
                'barber_id' => $user->barber_id
            ]);

            DB::commit();

            return response()->json(['message'=>'success', 'hair_dresser'=>$hairDresser],200);
        }catch(\Exception $e){
            Db::rollBack();
            return response()->json(['error'=>$e->getMessage()],500);
        }
    }

    public function showBarber()
    {
        $barbers = Barber::paginate($this->totalPage);

        return response()->json(['message'=>'success','barbers'=>$barbers], 200);
    }

    public function showHairdresser()
    {
        $user = Auth::user();
        
        $hairDresser = Hairdresser::where('barber_id',$user->barber_id)->paginate($this->totalPage);

        return response()->json(['hairdressers'=>$hairDresser],200);
    }

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
                'zip' 
            ]));

            return response()->json(['message'=>'success', 'barber'=>$barber], 200);
        }catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()], 500);
        }
    }

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
        } catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()], 500);
        }
    }

    public function deleteBarber($idBarber)
    {
        $user = Auth::user();
        
        $barber = Barber::find($idBarber);

        if($barber->id != $user->barber_id) return response()->json(['error'=>'Barberia does not belong to user'], 200);
        try{
            DB::beginTransaction();

            $barber->delete();

            DB::commit();
            return response()->json(['message'=>'Barber shop successfully deleted'], 200);
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error'=>$e->getMessage()], 500);
        }        
    }

    public function deleteHairdresser($idHairdresser)
        {
            $user = Auth::user();

            $hairDresser = Hairdresser::find($idHairdresser);

            if ($hairDresser->barber_id != $user->barber_id) return response()->json(['error'=>'Hairdresser does not currently belong to a barber shop'], 400);
            
            $hairDresser->delete();

            return response()->json(['message'=>'Hairdresser successfully deleted'],200);
        }
}