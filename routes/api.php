<?php

// Login/Signup APi
Route::post('{provider}/verifyAccessToken', ['uses' => 'ApiAuthController@verifyAccessToken']);
Route::post('refresh', ['uses' => 'ApiAuthController@refresh']);

Route::post('verifyFirebaseAccessToken', ['uses' => 'AuthFirebaseController@verifyAccessToken']);
Route::post('{provider}/verifyAdminAccessToken', ['uses' => 'AuthFirebaseController@verifyAdminAccessToken']);
Route::post('refreshFirebase', ['uses' => 'AuthFirebaseController@refresh']);

// Signup Form and General APi
Route::get('get_all_languages', ['uses' => 'PreciselyController@get_language']);
Route::get('get_filters', ['uses' => 'UtilController@get_filters']);
Route::get('get_all_countries', ['uses' => 'UtilController@get_all_countries']);

// Misc APi
Route::get('mentor/{slug}', ['uses' => 'PreciselyController@get_mentor_profile_from_slug']);
Route::get('get_location/{location_id}', ['uses' => 'PreciselyController@get_location']);
Route::get('get_funding_status/{id}', ['uses' => 'PreciselyController@get_funding_status']);
Route::post('show_comments', ['uses' => 'ApiRecordCommentController@show_comment']);
Route::post('add_version_code', ['uses' => 'UtilController@add_version_code']);
Route::get('generate_all_sitemap', ['uses' => 'UtilController@generate_all_sitemap']);
Route::get('generate_latest_sitemap', ['uses' => 'UtilController@generate_latest_sitemap']);

// Premium Plan
Route::get('list_premium_plans', ['uses' => 'PremiumSubscriptionController@list_premium_plans']);

Route::get('get_categories', ['uses' => 'LearnWithYoutubeController@getCategories']);
Route::get('get_all_categories', ['uses' => 'LearnWithYoutubeController@getAllCategories']);
Route::post('submit_feedback', ['uses' => 'LearnWithYoutubeController@submit_feedback']);

Route::get('get_notes', ['uses' => 'LWYResourceController@get_notes']);
Route::get('get_tests', ['uses' => 'LWYResourceController@get_tests']);

Route::get('get_resource_comments', ['uses' => 'LearnWithYoutubeController@get_resource_comments']);
Route::get('get_video_likes', ['uses' => 'LearnWithYoutubeController@get_resource_likes']);

Route::get('get_learning_path', ['uses' => 'LearnWithYoutubeController@get_learning_path']);
Route::get('get_next_level', ['uses' => 'LearnWithYoutubeController@getNextLevel']);

//Protected APIs via Auth Middleware
Route::group(['middleware' => 'auth:api'], function () {
    Route::group(['middleware' => ['login_status']], function () {
        // ----------Browse videos----------
        // Add new category in Browse section
        Route::post('add_new_category', ['uses' => 'LearnWithYoutubeController@addNewCategory']);

        // Profile APi
        Route::post('logout', ['uses' => 'ApiAuthController@logout']);

        Route::post('submit_user_profile', ['uses' => 'LearnWithYoutubeController@submit_user_profile']);
        Route::get('get_user_profile', ['uses' => 'LearnWithYoutubeController@get_user_profile']);
        Route::post('submit_mentor_profile', ['uses' => 'PreciselyController@submit_mentor_profile']);
        Route::post('submit_mentor_price', ['uses' => 'PreciselyController@update_mentor_price']);
        Route::get('get_mentor_profile', ['uses' => 'PreciselyController@get_mentor_profile']);

        // User Misc Data
        Route::post('save_user_language', ['uses' => 'PreciselyController@save_user_language']);
        Route::get('get_user_language', ['uses' => 'PreciselyController@get_user_language']);
        Route::post('save_user_filters', ['uses' => 'PreciselyController@save_user_filters']);
        Route::get('get_user_filters', ['uses' => 'PreciselyController@get_user_filters']);

        Route::post('save_comment', ['uses' => 'ApiRecordCommentController@save_comment']);
        Route::post('comment_reply', ['uses' => 'ApiRecordCommentController@save_reply_comment']);

        Route::post('mark_relevance', ['uses' => 'OpportunityController@mark_relevant']);

        // Resources
        Route::post('add_resource_comment', ['uses' => 'LearnWithYoutubeController@add_resource_comment']);
        Route::get('get_resource_likes', ['uses' => 'LearnWithYoutubeController@get_resource_likes']);

        Route::post('switch_video_like', ['uses' => 'LearnWithYoutubeController@switch_video_like']);

        Route::get('get_watch_history', ['uses' => 'LearnWithYoutubeController@getWatchHistory']);
        Route::post('save_to_watch_history', ['uses' => 'LearnWithYoutubeController@addToWatchHistory']);

        Route::post('upload_notes', ['uses' => 'LWYResourceController@upload_notes']);
        Route::post('upload_test', ['uses' => 'LWYResourceController@upload_test']);

//        Route::post('upload_test', ['uses' => 'LWYResourceController@upload_test']);

        // Social
        Route::get('get_followers', ['uses' => 'SocialController@get_followers']);
        Route::get('get_influencers', ['uses' => 'SocialController@get_influencers']);
        Route::post('start_following', ['uses' => 'SocialController@start_following']);

//        Chat
        Route::get('get_all_chats', ['uses' => 'ChatController@get_all_chats']);
        Route::get('get_chat_messages', ['uses' => 'ChatController@get_chat_messages']);
        Route::post('create_chat', ['uses' => 'ChatController@create_chat']);
        Route::post('create_support_chat', ['uses' => 'ChatController@create_support_chat']);
        Route::post('add_chat_user', ['uses' => 'ChatController@add_chat_user']);
        Route::post('send_message', ['uses' => 'ChatController@send_message']);
        Route::post('send_multimedia_message', ['uses' => 'ChatController@send_multimedia_message']);
        Route::post('change_chat_title', ['uses' => 'ChatController@change_chat_title']);

        // Notifications
        Route::post('send_notification', ['uses' => 'NotificationContoller@send_notification']);

        // Chemistry Universe
        Route::post('add_video_to_learning_path', ['uses' => 'LearnWithYoutubeController@add_video_to_learning_path']);
    });
});

Route::post('razorpay_demo_checkout', ['uses' => 'PreciselyController@razorpay_demo_checkout']);


Route::get('get_resource_from_slug', ['uses' => 'AWSApiController@get_resource_from_slug']);

//AWS API
Route::get('list_s3_files', ['uses' => 'AWSApiController@list_s3_files']);
Route::get('list_paginated_s3_files', ['uses' => 'AWSApiController@list_paginated_s3_files']);
Route::get('get_recommendations', ['uses' => 'AWSApiController@get_recommendations']);
Route::post('search_s3_files', ['uses' => 'AWSApiController@search_s3_files']);
Route::post('store_s3_file', ['uses' => 'AWSApiController@store_s3_file']);
Route::post('save_playlist', ['uses' => 'AWSApiController@save_playlist']);
Route::post('save_resource_thumbnail', ['uses' => 'AWSApiController@save_thumbnail']);
Route::post('upload_single_image', ['uses' => 'AWSApiController@upload_single_image']);
