<?php

use Illuminate\Http\Request;

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
Route::prefix('v1')->group(function(){
	Route::post('post/opportunity','UtilController@post_opportunity');
});
Route::prefix('v1')->group(function(){
	Route::post('');
});
/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

