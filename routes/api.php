<?php


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
    Route::get('/user/schedule', 'UsersController@scheduleUser');
    Route::get('/user', 'UsersController@showAll');
    Route::get('/user/{id}', 'UsersController@showSpecific')->where('id','[0-9]+');
    Route::put('/user/password', 'UsersController@changePassword');
   
    //Barber routes
    Route::post('/barber', 'BarbersController@store');
    Route::get('/barber', 'BarbersController@show');
    Route::post('/hairdresser', 'BarbersController@storeHairdresser');
    Route::get('/hairdresser', 'BarbersController@showHairdresser');
    Route::put('/barber/{id}', 'BarbersController@updateBarber')->where('id','[0-9]+');
    Route::put('/hairdresser/{id}', 'BarbersController@updateHairdresser')->where('id','[0-9]+');
    Route::delete('/hairdresser/{id}', 'BarbersController@deleteHairdresser')->where('id','[0-9]+');
    Route::delete('/barber/{id}', 'BarbersController@deleteBarber');
    
    //Auth routes
    Route::delete('/auth', 'UsersController@logout');
    
});

    //User routes
    Route::post('/user', 'UsersController@store');
    Route::post('/user/recover/email', 'UsersController@recoverEmail');
    Route::put('/user/password/update', 'UsersController@resetPassword');
    Route::get('/test', function(){
        return 'success';
    });

    //Auth routes
    Route::post('/auth', 'UsersController@login'); 
   
    

   

