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

//M2M APIs
Route::post('post/opportunity','UtilController@post_opportunity');


/*
	Android App APIs
*/
//Public APIs
Route::post('{provider}/verifyAccessToken',['uses'=>'ApiAuthController@verifyAccessToken']);
Route::post('refresh',['uses'=>'ApiAuthController@refresh']);
Route::get('get_all_languages',['uses'=>'PreciselyController@get_language']);
Route::get('get_filters',['uses'=>'PreciselyController@get_filters']);
Route::post('submit_user_profile',['uses'=>'PreciselyController@submit_profile']);

//Protected APIs
Route::group(['middleware' => 'auth:api'], function(){
	Route::post('logout',['uses'=>'ApiAuthController@logout']);
});




/*
	Testing APIs
*/
//Public APIs
Route::get('{provider}/authorize',['uses'=>'ApiAuthController@auth']);
Route::get('{provider}/login',['uses'=>'ApiAuthController@login']);
