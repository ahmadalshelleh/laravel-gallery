<?php

use App\Http\Middleware\UserAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');
Route::get('/getImages', 'UserController@getImages');
Route::post('/searchImages', 'UserController@searchImages');

Route::middleware([UserAuth::class])->group(function(){
  Route::post('/uploadImages', 'UserController@uploadImages');
  Route::get('/getUser', 'UserController@getUser');
  Route::post('/updateUser', 'UserController@updateUser');
});
