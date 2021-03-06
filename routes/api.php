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
Route::post('add_search_term',['uses' => 'SearchController@add_search_term']);

Route::get('get_ses_videos',['uses' => 'ChemistryUniverse@get_ses_videos']);
Route::get('is_supprot_to_chat_type_id', ['uses' => 'ChatController@is_supprot_to_chat_type_id']);
//Route::post('load_whatsapp_chat_into_db', ['uses' => 'ChatController@load_whatsapp_chat_into_db']);


// Protected APIs via Auth Middleware
//Route::group(['middleware' => 'admin_access'], function () {
Route::group(['middleware' => 'auth:api'], function () {

    // Searches term & search_user  Video_annotations
//      Route::post('add_search_term',['uses' => 'SearchController@add_search_term']);

    Route::group(['middleware' => ['login_status']], function () {
        Route::post('toggle_category_visibility', ['uses' => 'LearnWithYoutubeController@toggle_category_visibility']);
        Route::post('add_ses_video',['uses' => 'ChemistryUniverse@add_ses_video']);

//        Route::post('add_search_term',['uses' => 'SearchController@add_search_term']);
        Route::post('add_video_annotations',['uses' => 'VideoAnnotationController@add_video_annotations']);

        // ----------Browse videos----------

        // Add new category in Browse section
        Route::post('add_new_category', ['uses' => 'LearnWithYoutubeController@addNewCategory']);
        Route::post('remove_category', ['uses' => 'LearnWithYoutubeController@removeCategory']);

        // Add Image of category in category tabel
        Route::post('add_image_to_category', ['uses' => 'LearnWithYoutubeController@add_image_to_category']);

        // Keyword add to video
        Route::post('add_keyword_to_video', ['uses' => 'LearnWithYoutubeController@add_keyword_to_video']);

        // Profile APi
        Route::post('logout', ['uses' => 'ApiAuthController@logout']);

        Route::post('submit_user_profile', ['uses' => 'LearnWithYoutubeController@submit_user_profile']);
        Route::get('get_user_profile', ['uses' => 'LearnWithYoutubeController@get_user_profile']);
        Route::post('add_user_certificate', ['uses' => 'LearnWithYoutubeController@add_user_certificate']);

        // User Social Links add
        Route::post('add_user_facebook_link', ['uses' => 'LearnWithYoutubeController@add_user_facebook_link']);
        Route::post('add_user_instagram_link', ['uses' => 'LearnWithYoutubeController@add_user_instagram_link']);
        Route::post('add_user_github_link', ['uses' => 'LearnWithYoutubeController@add_user_github_link']);
        Route::post('add_user_twitter_url', ['uses' => 'LearnWithYoutubeController@add_user_twitter_url']);
        Route::post('add_user_linkedin_url', ['uses' => 'LearnWithYoutubeController@add_user_linkedin_url']);

        Route::post('save_comment', ['uses' => 'ApiRecordCommentController@save_comment']);
        Route::post('comment_reply', ['uses' => 'ApiRecordCommentController@save_reply_comment']);

        Route::post('mark_relevance', ['uses' => 'OpportunityController@mark_relevant']);

        // Resources
        Route::post('add_resource_comment', ['uses' => 'LearnWithYoutubeController@add_resource_comment']);
        Route::get('get_resource_likes', ['uses' => 'LearnWithYoutubeController@get_resource_likes']);

        Route::post('switch_video_like', ['uses' => 'LearnWithYoutubeController@switch_video_like']);

        Route::get('get_watch_history', ['uses' => 'LearnWithYoutubeController@getWatchHistory']);
        Route::post('save_to_watch_history', ['uses' => 'LearnWithYoutubeController@addToWatchHistory']);

        // Video_Rating
        Route::post('give_rate_video',['uses' => 'LearnWithYoutubeController@give_video_rating']);

        //Book-Mark Video 
        Route::post('user_bookmark_video',['uses' => 'LearnWithYoutubeController@user_bookmark_video']);

        //Attempt Test
        Route::post('attempt_test',['uses' => 'LearnWithYoutubeController@attempt_test']);

        Route::post('upload_notes', ['uses' => 'LWYResourceController@upload_notes']);
        Route::post('upload_test', ['uses' => 'LWYResourceController@upload_test']);

//        Route::post('upload_test', ['uses' => 'LWYResourceController@upload_test']);

//        Route::post('add_search_term',['uses' => 'SearchController@add_search_term']); 
//        Route::post('add_video_annotations',['uses' => 'VideoAnnotationController@add_video_annotations']);

        // Social
        Route::get('get_followers', ['uses' => 'SocialController@get_followers']);
        Route::get('get_influencers', ['uses' => 'SocialController@get_influencers']);
        Route::post('start_following', ['uses' => 'SocialController@start_following']);

//        Chat
//        Route::get('is_supprot_to_chat_type_id', ['uses' => 'ChatController@is_supprot_to_chat_type_id']);
        Route::post('load_whatsapp_chat_into_db', ['uses' => 'ChatController@load_whatsapp_chat_into_db']);
        Route::get('get_all_whatsapp_chats', ['uses' => 'ChatController@get_all_whatsapp_chats']);
        Route::get('get_whattsapp_chat_messages', ['uses' => 'ChatController@get_whattsapp_chat_messages']);

        Route::get('get_all_chats', ['uses' => 'ChatController@get_all_chats']);
        Route::get('get_chat_messages', ['uses' => 'ChatController@get_chat_messages']);
        Route::post('create_chat', ['uses' => 'ChatController@create_chat']);
        Route::post('create_support_chat', ['uses' => 'ChatController@create_support_chat']);
        Route::post('add_chat_user', ['uses' => 'ChatController@add_chat_user']);
        Route::post('send_message', ['uses' => 'ChatController@send_message']);
        Route::post('send_multimedia_message', ['uses' => 'ChatController@send_multimedia_message']);
        Route::post('change_chat_title', ['uses' => 'ChatController@change_chat_title']);

        Route::post('add_time_table', ['uses' => 'ChatController@add_time_table']);
        Route::post('add_teacher_document', ['uses' => 'ChatController@add_teacher_document']);
        Route::get('get_time_tables', ['uses' => 'ChatController@get_time_tables']);

        Route::post('add_chat_review', ['uses' => 'ChatController@add_chat_review']);
        Route::post('add_student_homework', ['uses' => 'ChatController@add_student_homework']);
        Route::get('search_filter_messages', ['uses' => 'ChatController@search_filter_messages']);
        Route::post('save_chat_message', ['uses' => 'ChatController@save_chat_message']);
        Route::post('classroom_chat_message', ['uses' => 'ChatController@classroom_chat_message']);

        //Attendance of classroom
        Route::post('add_teacher_attendance', ['uses' => 'ChatController@add_teacher_attendance']);
        Route::post('add_student_attendance', ['uses' => 'ChatController@add_student_attendance']);

        // Notifications
        Route::post('send_notification', ['uses' => 'NotificationContoller@send_notification']);

        // Chemistry Universe
        Route::post('add_video_to_learning_path', ['uses' => 'LearnWithYoutubeController@add_video_to_learning_path']);

    });
});
