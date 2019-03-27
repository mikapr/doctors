<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', 'HomeController#mo')->middleware('auth');

Route::group(['as' => 'doctors::'], function () {
    Route::get('/test', function () {
        dd(\Cache::get('blockedSlots'));
    });
    Route::get('/', ['as' => 'dashboard', 'uses' => 'HomeController@index']);
    Route::get('/services/', ['as' => 'services', 'uses' => 'HomeController@getServices']);
    Route::get('/doctors/{serviceId}', ['as' => 'doctors', 'uses' => 'HomeController@getDoctors']);
    Route::get('/schedule', ['as' => 'schedule', 'uses' => 'HomeController@getSchedule']);
    Route::get('/my-slots', ['as' => 'my-slots', 'uses' => 'HomeController@getMySlots']);
    Route::post('/block-slot', ['as' => 'block-slot', 'uses' => 'HomeController@postBlockSlot']);
    Route::post('/unblock-slot', ['as' => 'block-slot', 'uses' => 'HomeController@postUnBlockSlot']);
    Route::post('/remove-slot', ['as' => 'block-slot', 'uses' => 'HomeController@postRemoveSlot']);
    Route::post('/create-slot', ['as' => 'block-slot', 'uses' => 'HomeController@postCreateSlot']);
});

Auth::routes();