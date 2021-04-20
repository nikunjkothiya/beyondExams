<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Config;
use App\Video;

class YouTubeController extends Controller
{
  private $apiResponse;

  public function __construct(ApiResponse $apiResponse)
  {
    $this->apiResponse = $apiResponse;
  }

  public function get_video_all_details(Request $request)
  {
    DB::beginTransaction();
    $validator = Validator::make($request->all(), [
      'video_urls' => 'required|array',
      'video_urls.*' => 'string',
    ]);

    if ($validator->fails()) {
      return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
    }

    try {
      $final_response = [];
      foreach ($request->video_urls as $video_url) {
        $request = $video_url;
        $base_url = env('YOUTUBE_DATA_BASE_URL');
        $key = env('YOUTUBE_DATA_API');
        $maxResults = 50;

        $curl = curl_init();

        $url =  $base_url . '?part=snippet&q=' . $request . '&fields=items(id,snippet.title,snippet.description)&maxResults=' . $maxResults . '&key=' . $key;
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

        $items = json_decode($response);
        //dd($items->items);

        foreach ($items->items as $key => $result) {
          if (empty($result->id->videoId)) {
            continue;
          }
          $video_url = $result->id->videoId;
          $title = $result->snippet->title;
          $description = $result->snippet->description;
          $find_video = Video::where('url', $video_url)->first();
          if (!$find_video) {
            $add_new_video = new Video();
            $add_new_video->url = $video_url;
            $for_slug = str_replace(" ", "+", $title);
            $add_new_video->slug =  $for_slug . '' . substr(hash('sha256', mt_rand() . microtime()), 0, 5);
            $add_new_video->title = $title;
            $add_new_video->description = $description;
            $add_new_video->save();
          }

          $final_response[] = Video::with('notes.user:id,name,avatar', 'ratings', 'keywords', 'learning_path.category.user:id,name,avatar', 'annotations.total_vote')->where('url', $video_url)->first();
        }
      }

      DB::commit();
      return $this->apiResponse->sendResponse(200, 'Videos with all details get successfully', $final_response);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
    }
  }

     public function youtube_search_data(Request $request)
    {
        $request = $request->search;
        $base_url = env('YOUTUBE_DATA_BASE_URL');
        $key = env('YOUTUBE_DATA_API');
        $maxResults = 50;
        
        $curl = curl_init();

        $url =  $base_url.'?part=snippet&q='.$request.'&fields=items(id,snippet.title,snippet.description)&maxResults='.$maxResults.'&key='.$key;
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
        
        $items = json_decode($response);
        //dd($response);
        $final_response = [];
        foreach($items->items as $key=>$result)
        {
          if(empty($result->id->videoId)){
            continue;
          }
          $video_url = $result->id->videoId;
          $title = $result->snippet->title;
          $description = $result->snippet->description;
          $find_video = Video::where('url',$video_url)->first();
          if(!$find_video)
          {
            $add_new_video = new Video();
            $add_new_video->url = $video_url;
            $for_slug = str_replace(" ", "+", $title);
            $add_new_video->slug =  $for_slug .''. substr(hash('sha256', mt_rand() . microtime()), 0, 5);
            $add_new_video->title = $title;
            $add_new_video->description = $description;
            $add_new_video->save();
          }
        
        $final_response[] = Video::with('notes.user:id,name,avatar','ratings', 'keywords', 'learning_path.category.user:id,name,avatar','annotations')->where('url', $video_url)->first();
        }
        //dd($final_response);
        return $final_response; 
    } 
}
