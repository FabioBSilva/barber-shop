<?php

namespace App\Http\Controllers;

use Throwable;
use App\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\FieldValidators\SchedulesFieldValidator;


class ScheduleController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, SchedulesFieldValidator::storeSchedule());

        $user = Auth::user();

        try{
            DB::beginTransaction();

            $barber = $user->barber;
            if(!$barber) return response()->json(['error'=>'User is not a barber'],400);

            $schedule = Schedule::create([
                'date'      => $request['date'],
                'barber_id' => $user->barber_id

            ]);
            DB::commit();

            return response()->json(['message'=>'success', 'schedule'=>$schedule], 200);
        }catch(\Throwable $th){
            DB::rollback();
            return response()->json(['error'=>$th->getMessage()], 500);
        }
    }

    public function showSchedules()
    {
        $user = Auth::user();

        $schedules = Schedule::where('barber_id', $user->barber_id)->get();

        return response()->json(['schedules'=>$schedules], 200);
    }

    public function update()
    {
        $this->validate($request, SchedulesFieldValidator::updateSchedule());
        $user = Auth::user();

        try{
            DB::beginTransaction();

            $barber = $user->barber;
            if(!$barber) return response()->json(['error'=>'User is not a barber'],400);

            $schedule->update([
                'date' => $request->input('date', $schedule->date)
            ]);

            DB::commit();
            return response()->json(['message'=>'success'],200);
        }catch(\Throwable $th){
            DB::rollBack();
            return response()->json(['error'=>$th->getMessage()],500);
        }
    } 

   public function delete($idSchedule)
   {
       $user = Auth::user();
       try{
            $schedule = Schedule::find($idSchedule);

            $schedule->delete();

            return response()->json(['message'=>'Schedule deleted successfully'], 200);
       } catch(\Throwable $th){
        return response()->json(['error'=>$th->getMessage()],500);
       }
    }
    

}
