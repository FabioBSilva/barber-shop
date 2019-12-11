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
    Route::put('/user', 'UsersController@update');
    Route::get('/user/schedule', 'UsersController@scheduleUser');
    Route::get('/user', 'UsersController@showAll');
    Route::get('/user/{id}', 'UsersController@showSpecific')->where('id','[0-9]+');
    Route::put('/user/password', 'UsersController@changePassword');
   
    //Auth routes
    Route::delete('auth', 'UsersController@logout');
    Route::delete('/user', 'UsersController@delete');
});

    //User routes
    Route::post('/user', 'UsersController@store');
    Route::post('/user/recover/email', 'UsersController@recoverEmail');
    Route::put('/user/email/update', 'UsersController@resetPassword');

    //Auth routes
    Route::post('/auth', 'UsersController@login'); 
   
    

   

