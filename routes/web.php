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

Route::get('lang/{locale}','UtilController@locale');
Route::get('auth/{provider}', 'AuthController@redirect');
Route::get('auth/{provider}/callback', 'AuthController@callback');

Route::group(['middleware'=>['locale']],function(){
	Route::get('/',['as' => 'index','uses' => 'PageController@index']);
	Route::get('login',['as' => 'login','uses' => 'AuthController@login']);
	Route::get('opportunity/{slug}',['uses' => 'OpportunityController@get_opp']);
});

Route::group(['middleware'=>['locale','auth']],function(){
	Route::get('logout',['as'=>'logout','uses'=>'AuthController@logout']);
	Route::get('setup/{id}','PageController@setup');
	Route::post('setup/details',['as'=>'setup-details','uses'=>'PageController@setup_details']);
	Route::get('dashboard',['as'=>'dashboard','uses'=>'PageController@dashboard']);
	Route::get('dashboard/profile',['as'=>'profile','uses'=>'PageController@profile']);
	Route::post('dashboard/profile/save',['as'=>'profile.save','uses'=>'PageController@save_profile']);
	Route::get('dashboard/filter',['as'=>'filter','uses'=>'PageController@filter']);
	Route::post('save/filter',['as'=>'save-filter','uses'=>'PageController@save_filter']);
	Route::get('dashboard/saved-opp',['as'=>'saved-opp','uses'=>'PageController@save_opp']);
	Route::get('dashboard/subscription',['as'=>'subscription','uses'=>'SubscriptionController@subscription']);
	Route::get('dashboard/message',['as'=>'messages','uses'=>'PageController@message']);
	Route::post('opportunity/save',['as'=>'save','uses'=>'UtilController@save_opportunity']);
	Route::post('opportunity/unsave',['as'=>'unsave','uses'=>'UtilController@unsave_opportunity']);
	Route::get('nextopps',['uses'=>'UtilController@next_opps']);
	Route::get('nextsavedopps',['uses'=>'UtilController@next_saved_opps']);
	Route::post('checkout',['as'=>'checkout','uses'=>'SubscriptionController@checkout']);
	Route::post('success',['as'=>'success','uses'=>'SubscriptionController@success']);
	Route::post('failure',['as'=>'failure','uses'=>'SubscriptionController@failure']);
    Route::post('opportunity/request_guidance',['as'=>'request-guidance','uses'=>'UtilController@request_guidance']);
});
