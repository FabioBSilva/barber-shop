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
    private $totalPage = 5; 
    //POST method: create a schedule
    //Route: /schedule
    public function store(Request $request)
    {
        $this->validate($request, SchedulesFieldValidator::storeSchedule());

        $user = Auth::user();

        try{

            $barber = $user->barber;
            if(!$barber) return response()->json(['error'=>'User is not a barber'],400);
            
            $schedule = Schedule::create([
                'hour'      => $request['hour'],
                'barber_id' => $user->barber_id

            ]);

            // $schedules = Schedule::all();
            // foreach($schedules as $schedule){
            //     $schedule->date = date("d-m-Y", strtotime($schedule->date));
            // }

            return response()->json(['message'=>'success', 'schedule'=>$schedule], 200);
        }catch(\Throwable $th){
            return response()->json(['error'=>$th->getMessage()], 500);
        }
    }

    //GET method: Show all times belonging to barber shop
    //Route: /schedule
    public function showSchedules()
    {
        $user = Auth::user();

        $schedules = Schedule::where('barber_id', $user->barber_id)->paginate($this->totalPage);

        return response()->json(['schedules'=>$schedules], 200);
    }

    //GET method: Get all the barber shop times
    //Route: /schedule/user
    public function showUserSchedules(Request $request)
    {
        $user = Auth::user();

        $schedules = Schedule::where('schedules.barber_id', $user->barber_id)
        ->leftJoin('users', 'users.schedule_id', 'schedules.id')
        ->leftJoin('hairdressers', 'users.hairdresser_id', 'hairdressers.id')
        ->join('barbers', 'barbers.id', 'schedules.barber_id')
        ->selectRaw('schedules.id as schedule_id, users.name as user, hairdressers.name as hairdresser, schedules.hour, barbers.name as barber, barbers.id as barber_id')
        ->paginate($request->query('per_page', 5));
        
        return response()->json(['schedules'=>$schedules], 200);
    }

    //PUT method: allows change in time
    //Route: /schedule/{id}
    public function update(Request $request, $idSchedule)
    {
        $this->validate($request, SchedulesFieldValidator::updateSchedule());
        $user = Auth::user();

        try{

            $barber = $user->barber;
            if(!$barber) return response()->json(['error'=>'User is not a barber'],400);

            $schedule = Schedule::find($idSchedule);

            if ($schedule->barber_id != $user->barber_id) return response()->json(['error'=>'Schedule does not currently belong to a barber shop'], 400);

            $schedule->update([
                'hour' => $request->input('hour', $schedule->hour)
            ]);

            return response()->json(['message'=>'success','schedule'=>$schedule],200);
        }catch(\Throwable $th){
            return response()->json(['error'=>$th->getMessage()],500);
        }
    } 

    //DELETE method: Clears a schedule
    //Route: /schedule/{id}
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
