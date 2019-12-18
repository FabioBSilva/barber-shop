<?php

use App\Events\TestEvent;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:api')->group(function () {

    //User routes
    Route::post('/user/update', 'UsersController@update');
    Route::delete('/user', 'UsersController@delete');
    Route::get('/user/schedule', 'UsersController@getScheduleUser');
    Route::get('/user', 'UsersController@showAll');
    Route::get('/user/{id}', 'UsersController@showSpecific')->where('id','[0-9]+');
    Route::put('/user/password', 'UsersController@changePassword');
    Route::post('/schedule/{scheduleid}/hairdresser/{hairdresserid}', 'UsersController@storeSchedulesUser')->where('id','[0-9]+');
    Route::delete('/schedule/{scheduleid}/hairdresser/{hairdresserid}', 'UsersController@deleteScheduleUser')->where('id','[0-9]+');
   
    //Barber routes
    Route::post('/barber', 'BarbersController@store');
    Route::get('/barber', 'BarbersController@showBarber');
    Route::get('/hairdresser', 'BarbersController@showHairdresser');
    Route::get('/barber/{id}', 'BarbersController@showBarberSpecific');
    Route::post('/hairdresser', 'BarbersController@storeHairdresser');
    Route::post('/barber/update/{id}', 'BarbersController@updateBarber')->where('id','[0-9]+');
    Route::put('/hairdresser/{id}', 'BarbersController@updateHairdresser')->where('id','[0-9]+');
    Route::delete('/hairdresser/{id}', 'BarbersController@deleteHairdresser')->where('id','[0-9]+');
    Route::delete('/barber/{id}', 'BarbersController@deleteBarber')->where('id','[0-9]+');

    //Schedule routes
    Route::post('/schedule', 'ScheduleController@store');
    Route::put('/schedule/{id}', 'ScheduleController@update');
    Route::delete('/schedule/{id}', 'ScheduleController@delete');
    Route::get('/schedule/user', 'ScheduleController@showUserSchedules');
    Route::get('/schedule', 'ScheduleController@showSchedules');
    
    //Auth routes
    Route::delete('/auth', 'UsersController@logout');
    
});

    //User routes
    Route::post('/user', 'UsersController@store');
    Route::post('/user/recover/email', 'UsersController@recoverEmail');
    Route::put('/user/password/update', 'UsersController@resetPassword');
    Route::get('/test', function(){
        return event(new TestEvent());
    });

    //Auth routes
    Route::post('/auth', 'UsersController@login'); 
