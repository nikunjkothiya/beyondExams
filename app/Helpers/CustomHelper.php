<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

function commonUploadFile($storage_path, $file_path)
{
    $fileName = time().'.'.$file_path->getClientOriginalExtension();
    $file_path->move(public_path($storage_path), $fileName);
    return $storage_path.''.$fileName;
}

function youtube_data_api($video_url)
{
    $base_video_url = env('YOUTUBE_DATA_BASE_VIDEO_URL');
    $key = env('YOUTUBE_DATA_API');

    $curl = curl_init();
    $url =  $base_video_url . '?part=snippet&id=' . $video_url . '&fields=items(id,snippet.title,snippet.description)&key=' . $key;
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    //return $response;
    $data = json_decode($response);
    /* if(empty($data->items)){
        return false;
    } */
    $newArray = [];
    $title = $data->items[0]->snippet->title;
    
    $for_slug = str_replace(" ", "+", $title);
    $newArray['slug'] =  $for_slug .''. substr(hash('sha256', mt_rand() . microtime()), 0, 5);
    $newArray['title'] = $title;
 //   $newArray['original_title'] = $for_slug;
    $newArray['description'] = $data->items[0]->snippet->description;

    return $newArray;
}

function youtube_video_time_get($video_url)
{
    $base_video_url = env('YOUTUBE_DATA_BASE_VIDEO_URL');
    $key = env('YOUTUBE_DATA_API');

    $curl = curl_init();
    $url =  $base_video_url . '?part=contentDetails&id=' . $video_url . '&key=' . $key;
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    //return $response;
    $data = json_decode($response);
    $vinfo = $data->items[0]->contentDetails->duration;
    
    $interval = new DateInterval($vinfo);
    $time_in_sec = ($interval->h * 3600 + $interval->i * 60 + $interval->s);

    return $time_in_sec;
}