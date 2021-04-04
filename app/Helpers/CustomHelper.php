<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

function commonUploadImage($storage_path, $file_path)
{
    $imageName = time().'.'.$file_path->getClientOriginalExtension();
    $file_path->move(public_path($storage_path), $imageName);
    return $storage_path.''.$imageName;
}

function youtube_data_api($video_url)
{
    $base_video_url = env('YOUTUBE_DATA_BASE_VIDEO_URL');
    $key = env('YOUTUBE_DATA_API');

    $curl = curl_init();
    $url =  $base_video_url . '?part=snippet&id=' . $video_url . '&fields=items(id,snippet.title)&key=' . $key;
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
    $title = $data->items[0]->snippet->title;
    $title = str_replace(" ", "+", $title);

    return $title;
}