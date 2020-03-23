<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiResponse;
use Storage;

class AWSApiController extends Controller
{
    private $apiResponse;

 	public function __construct(ApiResponse $apiResponse){
        $this->apiResponse=$apiResponse;
     }

    public function list_s3_files(){
        try{
        $all_files = Storage::disk('s3')->files("/video");
        $urls = [];
        
        foreach($all_files as $file){
            $urls[] = 'precisely-test1.s3.ap-south-1.amazonaws.com/' . $file;
        }
        $processed =[];
        foreach($urls as $url){
            $processed[] = str_replace(' ', '+', $url);
        }

        $data = [];
        foreach($processed as $pro){
            $data[] = array('url'=>$pro,'thumbnail'=>null,'type'=>null,'length'=>null,'title'=>null,'author'=>null,'designation'=>null,'profile_pic'=>null);
        }
        
        return $this->apiResponse->sendResponse(200, 'Success', $data);
        }catch(Exception $e){
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', null);
            }
    }

    public function search_s3_files(Request $request){
        try{
        $all_files = Storage::disk('s3')->files("/video");
        $urls = [];
        
        foreach($all_files as $file){
            $urls[] = 'precisely-test1.s3.ap-south-1.amazonaws.com/' . $file;
        }

        $splited = [];
        foreach($urls as $url){
            $splited[] = strtolower(explode ("video/", $url)[1]);
        }

        $req_files = [];
        $keyword = strtolower($request->keyword);

        foreach($splited as $spl){
            $exists = strpos($spl, $keyword);
            if ($exists !== false) {
                $req_files[] = $spl;
            }
        }
        
        return $this->apiResponse->sendResponse(200, 'Success', $req_files);
        }catch(Exception $e){
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', null);
            }
    }
}
