<?php

use Illuminate\Http\Request;

//M2M APIs
Route::post('post/opportunity', 'UtilController@post_opportunity');

// Legacy Migrations API
// Use tinker for this
// Route::get('migrate_legacy_users', ['uses' => 'LegacyDataController@migrate_legacy_users']);
// Route::get('migrate_legacy_chats', ['uses' => 'LegacyDataController@migrate_legacy_chats']);

// Login/Signup APi
Route::post('{provider}/verifyAccessToken', ['uses' => 'ApiAuthController@verifyAccessToken']);
Route::post('refresh', ['uses' => 'ApiAuthController@refresh']);
Route::post('organisation/{provider}/verifyAccessToken', ['uses' => 'ApiAuthOrganisationController@verifyAccessToken']);
Route::post('organisation/refresh', ['uses' => 'ApiAuthOrganisationController@refresh']);

// Signup Form and General APi
Route::get('get_all_languages', ['uses' => 'PreciselyController@get_language']);
Route::get('get_filters', ['uses' => 'PreciselyController@get_filters']);
Route::get('get_all_countries', ['uses' => 'PreciselyController@get_all_countries']);
Route::get('get_all_domains', ['uses' => 'PreciselyController@get_all_domains']);

// Misc APi
Route::get('opportunity/{slug}', ['uses' => 'ApiOpportunityController@get_opp_by_slug']);
Route::get('get_location/{location_id}', ['uses' => 'PreciselyController@get_location']);
Route::get('get_funding_status/{id}', ['uses' => 'PreciselyController@get_funding_status']);
Route::post('show_comments', ['uses' => 'ApiRecordCommentController@show_comment']);
Route::post('add_version_code', ['uses' => 'UtilController@add_version_code']);
Route::get('generate_all_sitemap', ['uses' => 'UtilController@generate_all_sitemap']);
Route::get('generate_latest_sitemap', ['uses' => 'UtilController@generate_latest_sitemap']);

// Premium Plan
Route::get('list_premium_plans', ['uses' => 'PremiumSubscriptionController@list_premium_plans']);

//Protected APIs via Auth Middleware
Route::group(['middleware' => 'auth:api'], function () {

    // Profile APi
    Route::post('logout', ['uses' => 'ApiAuthController@logout']);
    Route::post('submit_user_profile', ['uses' => 'PreciselyController@submit_user_profile']);
    Route::get('get_user_profile', ['uses' => 'PreciselyController@get_user_profile']);
    Route::post('submit_mentor_profile', ['uses' => 'PreciselyController@submit_mentor_profile']);
    Route::get('get_mentor_profile', ['uses' => 'PreciselyController@get_mentor_profile']);

    // User Misc Data
    Route::post('save_user_language', ['uses' => 'PreciselyController@save_user_language']);
    Route::get('get_user_language', ['uses' => 'PreciselyController@get_user_language']);
    Route::post('save_user_filters', ['uses' => 'PreciselyController@save_user_filters']);
    Route::get('get_user_filters', ['uses' => 'PreciselyController@get_user_filters']);
    Route::post('save_user_domains', ['uses' => 'PreciselyController@save_user_domains']);
    Route::get('get_user_domains', ['uses' => 'PreciselyController@get_user_domains']);

    // Opportuinities APi
    Route::get('opportunities', ['uses' => 'ApiOpportunityController@get_opportunities']);
    Route::post('unsave_opportunity', ['uses' => 'PreciselyController@unsave_opportunity']);
    Route::post('save_opportunity', ['uses' => 'PreciselyController@save_opportunity']);
    Route::get('show_saved_opportunity', ['uses' => 'PreciselyController@show_saved_opportunity']);

    Route::post('save_comment', ['uses' => 'ApiRecordCommentController@save_comment']);
    Route::post('comment_reply', ['uses' => 'ApiRecordCommentController@save_reply_comment']);

    Route::get('get_user_views_opp', ['uses' => 'ApiOpportunityController@get_user_views_opp']);
    Route::post('save_user_views_opp', ['uses' => 'ApiOpportunityController@save_user_views_opp']);

    // Resource Locking
    Route::get('get_user_keys', ['uses' => 'ResourceController@get_user_keys']);
    // Route::get('get_author_keys', ['uses' => 'ResourceController@get_author_keys']);
    // Route::post('save_new_key', ['uses' => 'ResourceController@save_new_key']);
    // Route::post('lock_resource', ['uses' => 'ResourceController@lock_resource']);
    Route::post('upload_notes', ['uses' => 'ResourceController@upload_notes']);
    Route::post('upload_test', ['uses' => 'ResourceController@upload_test']);
    Route::post('submit_test_score', ['uses' => 'ResourceController@submit_test_score']);
    Route::get('get_test_scores', ['uses' => 'ResourceController@get_test_scores']);
    Route::post('add_resource_reply', ['uses' => 'ResourceController@add_resource_reply']);
    Route::post('add_resource_comment', ['uses' => 'ResourceController@add_resource_comment']);

    // Premium Subscription
    Route::get('get_subscriptions', ['uses' => 'PremiumSubscriptionController@get_subscriptions']);
    Route::post('add_days_to_premium', ['uses' => 'PremiumSubscriptionController@add_days_to_premium']);
    Route::post('premium_checkout', ['uses' => 'PremiumSubscriptionController@premium_checkout']);

    // Chats Integration
    Route::post('submit_guidance_request', ['uses'=>'UtilController@submit_guidance_request']);
    Route::get('get_all_chats', ['uses' => 'ChatController@get_all_chats']);
    Route::get('get_chat_messages', ['uses' => 'ChatController@get_chat_messages']);
    Route::post('create_group_chat', ['uses' => 'ChatController@create_group_chat']);
    Route::post('create_opportunity_chat', ['uses' => 'ChatController@create_opportunity_chat']);
    Route::post('create_support_chat', ['uses' => 'ChatController@create_support_chat']);
    Route::post('add_chat_user', ['uses' => 'ChatController@add_chat_user']);
    Route::post('add_chat_admin', ['uses' => 'ChatController@add_chat_admin']);
    Route::post('add_chat_operator', ['uses' => 'ChatController@add_chat_operator']);
    Route::post('send_message', ['uses' => 'ChatController@send_message']);
    Route::post('send_multimedia_message', ['uses' => 'ChatController@send_multimedia_message']);
    Route::post('add_student_firebase_id', ['uses' => 'ChatController@add_student_firebase_id']);
    Route::post('add_admin_firebase_id', ['uses' => 'ChatController@add_admin_firebase_id']);
    Route::post('change_chat_title', ['uses' => 'ChatController@change_chat_title']);
    Route::get('get_all_mentors', ['uses' => 'ChatController@get_all_mentors']);
    Route::post('assign_mentor', ['uses' => 'ChatController@assign_mentor']);
    Route::post('create_anonymous_chat', ['uses' => 'ChatController@create_anonymous_chat']);
    Route::get('get_chat_from_hash', ['uses' => 'ChatController@get_chat_from_hash']);
    Route::post('send_message_through_hash', ['uses' => 'ChatController@send_message_through_hash']);
    
    // Chat Categories
    Route::get('get_categories', ['uses' => 'ChatController@get_categories']);
    Route::post('add_category', ['uses' => 'ChatController@add_category']);
    Route::post('assign_category', ['uses' => 'ChatController@assign_category']);

    // Notifications
    Route::post('send_notification', ['uses' => 'NotificationContoller@send_notification']);

    // Old Premium
    // Route::get('subscription', ['uses' => 'SubscriptionController@subscription']);
    // Route::post('checkout', ['uses' => 'SubscriptionController@checkout']);
    // Route::post('success', ['uses' => 'SubscriptionController@success']);
    // Route::post('failure', ['uses' => 'SubscriptionController@failure']);
});

// Learnage Broadcasting
Route::get('get_broadcast_sessions', ['uses' => 'LearnageBroadcastingController@get_broadcast_sessions']);
Route::get('get_scheduled_sessions', ['uses' => 'LearnageBroadcastingController@get_scheduled_sessions']);
Route::post('add_session', ['uses' => 'LearnageBroadcastingController@add_session']);
Route::post('update_session', ['uses' => 'LearnageBroadcastingController@update_session']);
Route::post('verify_user', ['uses' => 'LearnageBroadcastingController@verify_user']);

// Resource Locking
Route::get('get_author_keys', ['uses' => 'ResourceController@get_author_keys']);
Route::post('save_new_key', ['uses' => 'ResourceController@save_new_key']);
Route::post('lock_resource', ['uses' => 'ResourceController@lock_resource']);


//AWS API
Route::get('list_s3_files', ['uses' => 'AWSApiController@list_s3_files']);
Route::get('list_paginated_s3_files', ['uses' => 'AWSApiController@list_paginated_s3_files']);
Route::get('get_recommendations', ['uses' => 'AWSApiController@get_recommendations']);
Route::post('search_s3_files', ['uses' => 'AWSApiController@search_s3_files']);
Route::post('store_s3_file', ['uses' => 'AWSApiController@store_s3_file']);
Route::post('save_playlist', ['uses' => 'AWSApiController@save_playlist']);
Route::post('save_resource_thumbnail', ['uses' => 'AWSApiController@save_thumbnail']);
Route::get('get_resource_from_slug', ['uses' => 'AWSApiController@get_resource_from_slug']);


// Opportuinity Navigation
Route::get('next_opportunity_by_slug', ['uses' => 'ApiOpportunityController@get_next_opportunity']);
Route::get('previous_opportunity_by_slug', ['uses' => 'ApiOpportunityController@get_previous_opportunity']);


//Route::group(['middleware'=>'auth:organisation'], function (){
Route::post('organisation/post/opportunity', ['uses' => 'OrganisationController@post_opportunity']);
//});


/*
	Testing APIs
*/
Route::get('{provider}/authorize', ['uses' => 'ApiAuthController@auth']);
Route::get('{provider}/login', ['uses' => 'ApiAuthController@login']);

Route::get('organisation/{provider}/authorize', ['uses' => 'ApiAuthOrganisationController@auth']);
Route::get('organisation/{provider}/login', ['uses' => 'ApiAuthOrganisationController@login']);

Route::post('segment_analytics', ['uses' => 'PreciselyController@segment_analytics']);

Route::get('analytics', ['uses' => 'OrganisationController@analytics']);

// Route::post('submit_guidance_request', ['uses'=>'UtilController@submit_guidance_request']);

Route::get('get_opportunity_stack', ['uses' => 'ApiOpportunityController@get_opportunity_stack']);
