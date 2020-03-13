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
Route::post('save_user_filters',['uses'=>'PreciselyController@save_user_filters']);
Route::post('save_user_language',['uses'=>'PreciselyController@save_user_language']);

Route::get('get_all_countries',['uses'=>'PreciselyController@get_all_countries']);
Route::get('opportunity/{slug}',['uses' => 'ApiOpportunityController@get_opp']);
Route::get('get_location/{location_id}',['uses' => 'PreciselyController@get_location']);
Route::get('get_funding_status/{id}',['uses' => 'PreciselyController@get_funding_status']);

//Protected APIs
Route::group(['middleware' => 'auth:api'], function(){
	Route::post('logout',['uses'=>'ApiAuthController@logout']);
    Route::post('unsave_opportunity',['uses'=>'PreciselyController@unsave_opportunity']);
	Route::post('save_opportunity',['uses'=>'PreciselyController@save_opportunity']);
});

/*
	Testing APIs
*/
//Public APIs
Route::get('{provider}/authorize',['uses'=>'ApiAuthController@auth']);
Route::get('{provider}/login',['uses'=>'ApiAuthController@login']);

Route::group(['middleware' => 'auth:api'], function(){
	
	Route::post('comment',['uses' => 'ApiRecordCommentController@save_comment']);
	Route::post('comment_reply',['uses' => 'ApiRecordCommentController@save_reply_comment']);
	#Route::get('comment/{opportunity_id}',['uses' => 'ApiRecordCommentController@show_comment']);
	Route::post('opportunities',['uses' => 'ApiOpportunityController@get_opp_by_tags']);
	

	Route::get('dashboard/subscription',['uses'=>'SubscriptionController@subscription']);
	Route::post('checkout', ['uses'=>'SubscriptionController@checkout']);
	Route::post('success',['uses'=>'SubscriptionController@success']);
	Route::post('failure',['uses'=>'SubscriptionController@failure']);

	Route::post('submit_user_profile',['uses'=>'PreciselyController@submit_profile']);
	Route::get('get_profile',['uses'=>'PreciselyController@get_profile']);
});

