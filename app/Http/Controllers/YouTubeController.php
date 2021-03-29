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

class YouTubeController extends Controller
{
    public function youtube_search_data(Request $request)
    {
        $request = $request->search;
        $base_url = env('YOUTUBE_DATA_BASE_URL');
        $key = env('YOUTUBE_DATA_API');
        
        $curl = curl_init();

        $url =  $base_url.'?part=snippet&q='.$request.'&key='.$key;
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
        
        return $response;   
    }

}
