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
Route::post('post/opportunity', 'UtilController@post_opportunity');


/*
	Android App APIs
*/
//Public APIs
Route::post('{provider}/verifyAccessToken', ['uses' => 'ApiAuthController@verifyAccessToken']);
Route::post('refresh', ['uses' => 'ApiAuthController@refresh']);
Route::post('organisation/{provider}/verifyAccessToken', ['uses' => 'ApiAuthOrganisationController@verifyAccessToken']);
Route::post('organisation/refresh', ['uses' => 'ApiAuthOrganisationController@refresh']);

Route::get('get_all_languages', ['uses' => 'PreciselyController@get_language']);
Route::get('get_filters', ['uses' => 'PreciselyController@get_filters']);


Route::get('get_all_countries', ['uses' => 'PreciselyController@get_all_countries']);
Route::get('opportunity/{slug}', ['uses' => 'ApiOpportunityController@get_opp']);
Route::get('get_location/{location_id}', ['uses' => 'PreciselyController@get_location']);
Route::get('get_funding_status/{id}', ['uses' => 'PreciselyController@get_funding_status']);
Route::post('show_comments', ['uses' => 'ApiRecordCommentController@show_comment']);
//Route::get('opportunities',['uses' => 'ApiOpportunityController@get_opportunities']);

//Protected APIs
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('opportunities', ['uses' => 'ApiOpportunityController@get_opportunities']);
    Route::post('logout', ['uses' => 'ApiAuthController@logout']);

    Route::post('unsave_opportunity', ['uses' => 'PreciselyController@unsave_opportunity']);
    Route::post('save_opportunity', ['uses' => 'PreciselyController@save_opportunity']);
    Route::get('show_saved_opportunity', ['uses' => 'PreciselyController@show_saved_opportunity']);

    Route::post('save_comment', ['uses' => 'ApiRecordCommentController@save_comment']);
    Route::post('comment_reply', ['uses' => 'ApiRecordCommentController@save_reply_comment']);

	//	Route::post('opportunities',['uses' => 'ApiOpportunityController@get_opportunities']);


    Route::get('subscription', ['uses' => 'SubscriptionController@subscription']);
    Route::post('checkout', ['uses' => 'SubscriptionController@checkout']);
    Route::post('success', ['uses' => 'SubscriptionController@success']);
    Route::post('failure', ['uses' => 'SubscriptionController@failure']);

    Route::post('submit_user_profile', ['uses' => 'PreciselyController@submit_user_profile']);
    Route::post('submit_mentor_profile', ['uses' => 'PreciselyController@submit_mentor_profile']);
    Route::get('get_user_profile', ['uses' => 'PreciselyController@get_user_profile']);
    Route::get('get_mentor_profile', ['uses' => 'PreciselyController@get_mentor_profile']);

    Route::post('save_user_language', ['uses' => 'PreciselyController@save_user_language']);
    Route::post('save_user_filters', ['uses' => 'PreciselyController@save_user_filters']);

    Route::get('get_user_language', ['uses' => 'PreciselyController@get_user_language']);
    Route::get('get_user_filters', ['uses' => 'PreciselyController@get_user_filters']);

});

Route::get('next_opportunity_by_slug', ['uses' => 'ApiOpportunityController@get_next_opportunity']);
Route::get('previous_opportunity_by_slug', ['uses' => 'ApiOpportunityController@get_previous_opportunity']);

//Route::group(['middleware'=>'auth:organisation'], function (){
Route::post('organisation/post/opportunity', ['uses' => 'OrganisationController@post_opportunity']);
//});
/*
	Testing APIs
*/
//Public APIs
Route::get('{provider}/authorize', ['uses' => 'ApiAuthController@auth']);
Route::get('{provider}/login', ['uses' => 'ApiAuthController@login']);

Route::get('organisation/{provider}/authorize', ['uses' => 'ApiAuthOrganisationController@auth']);
Route::get('organisation/{provider}/login', ['uses' => 'ApiAuthOrganisationController@login']);

//AWS API
Route::get('list_s3_files', ['uses' => 'AWSApiController@list_s3_files']);
Route::get('get_recommendations', ['uses' => 'AWSApiController@get_recommendations']);
Route::post('search_s3_files', ['uses' => 'AWSApiController@search_s3_files']);
Route::post('store_s3_file', ['uses' => 'AWSApiController@store_s3_file']);
Route::post('save_playlist', ['uses' => 'AWSApiController@save_playlist']);
Route::post('save_resource_thumbnail', ['uses' => 'AWSApiController@save_thumbnail']);
Route::get('get_resource_from_slug', ['uses' => 'AWSApiController@get_resource_from_slug']);

Route::post('segment_analytics', ['uses' => 'PreciselyController@segment_analytics']);

Route::get('analytics', ['uses' => 'OrganisationController@analytics']);

Route::get('get_user_keys', ['uses' => 'ResourceLockController@get_user_keys']);
Route::get('get_author_keys', ['uses' => 'ResourceLockController@get_author_keys']);
Route::post('save_new_key', ['uses' => 'ResourceLockController@save_new_key']);
Route::post('lock_resource', ['uses' => 'ResourceLockController@lock_resource']);
