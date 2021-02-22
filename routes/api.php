<?php

// Login/Signup APi
Route::post('{provider}/verifyAccessToken', ['uses' => 'ApiAuthController@verifyAccessToken']);
Route::post('refresh', ['uses' => 'ApiAuthController@refresh']);

Route::post('verifyFirebaseAccessToken', ['uses' => 'AuthFirebaseController@verifyAccessToken']);
Route::post('{provider}/verifyAdminAccessToken', ['uses' => 'AuthFirebaseController@verifyAdminAccessToken']);
Route::post('refreshFirebase', ['uses' => 'AuthFirebaseController@refresh']);

// Signup Form and General APi
Route::get('get_filters', ['uses' => 'UtilController@get_filters']);
Route::get('get_all_countries', ['uses' => 'UtilController@get_all_countries']);

// Misc APi
Route::post('show_comments', ['uses' => 'ApiRecordCommentController@show_comment']);
Route::post('add_version_code', ['uses' => 'UtilController@add_version_code']);
Route::get('generate_all_sitemap', ['uses' => 'UtilController@generate_all_sitemap']);
Route::get('generate_latest_sitemap', ['uses' => 'UtilController@generate_latest_sitemap']);

Route::get('get_categories', ['uses' => 'LearnWithYoutubeController@getCategories']);
Route::get('get_all_categories', ['uses' => 'LearnWithYoutubeController@getAllCategories']);
Route::get('get_all_categories_hierarchically', ['uses' => 'LearnWithYoutubeController@getAllCategoriesHierarchically']);
Route::post('submit_feedback', ['uses' => 'LearnWithYoutubeController@submit_feedback']);

Route::get('get_notes', ['uses' => 'LWYResourceController@get_notes']);
Route::get('get_tests', ['uses' => 'LWYResourceController@get_tests']);

Route::get('get_resource_comments', ['uses' => 'LearnWithYoutubeController@get_resource_comments']);
Route::get('get_video_likes', ['uses' => 'LearnWithYoutubeController@get_resource_likes']);

Route::get('get_learning_path', ['uses' => 'LearnWithYoutubeController@get_learning_path']);
Route::get('get_next_level', ['uses' => 'LearnWithYoutubeController@getNextLevel']);

Route::get('get_most_searched_terms',['uses' => 'SearchController@get_most_searched_terms']);
Route::get('get_video_annotations',['uses' => 'VideoAnnotationController@get_video_annotations']);

Route::get('get_ses_videos',['uses' => 'ChemistryUniverseController@get_ses_videos']);

//Protected APIs via Auth Middleware
Route::group(['middleware' => 'auth:api'], function () {

// Searches term & search_user  Video_annotations
//Route::post('add_search_term',['uses' => 'SearchController@add_search_term']);

    Route::group(['middleware' => ['login_status']], function () {
        Route::post('toggle_category_visibility', ['uses' => 'LearnWithYoutubeController@toggle_category_visibility']);
        Route::post('add_ses_video',['uses' => 'ChemistryUniverseController@add_ses_video']);

        Route::post('add_search_term',['uses' => 'SearchController@add_search_term']);
        Route::post('add_video_annotations',['uses' => 'VideoAnnotationController@add_video_annotations']);

        // ----------Browse videos----------
        // Add new category in Browse section
        Route::post('add_new_category', ['uses' => 'LearnWithYoutubeController@addNewCategory']);
        Route::post('remove_category', ['uses' => 'LearnWithYoutubeController@removeCategory']);

        // Profile APi
        Route::post('logout', ['uses' => 'ApiAuthController@logout']);

        Route::post('submit_user_profile', ['uses' => 'LearnWithYoutubeController@submit_user_profile']);
        Route::get('get_user_profile', ['uses' => 'LearnWithYoutubeController@get_user_profile']);

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
