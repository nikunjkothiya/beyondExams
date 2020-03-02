<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiResponse;
use App\Language;
use App\Opportunity;
use App\User;
use App\ListComments;
use App\UserComments;
use App\OpportunityComments;
use App\RecordReply;
use Validator;
use Auth;
use DB;

class ApiRecordCommentController extends Controller
{
    private $apiResponse;

 	public function __construct(ApiResponse $apiResponse){
        $this->apiResponse=$apiResponse;
     }
     
    public function save_comment(Request $request)
    {
        try {            
            $flag = 0;
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                
                $check = Validator::make($request->all(), [
                    'message' => 'required|string',
                    'opportunity_id' => 'required|string',
                    ]);

                if ($check->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Bad Request', $check->errors());
                }

                $data = new ListComments;
                $data->message = $request->message;
                $data->save();

                $comment_id = DB::table('list_comments')->orderBy('id', 'DESC')->first()->id;

                $data = new UserComments; 
                $data->user_id=$user->id;
                $data->comment_id = $comment_id;
                $data->save();

                $data = new OpportunityComments;
                $data->opportunity_id = $request->opportunity_id;
                $data->comment_id = $comment_id;
                $data->save();

                $response_data["message"] = "Comment recorded";

                return $this->apiResponse->sendResponse(200,'Success',null);
            }
            

        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(404,'Page Not Found',null);
        }
    }

    public function save_reply_comment(Request $request)
    {
        try {            
            $flag = 0;
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                $flag = $this->txnflag->check_subscription(Auth::user()->id);
                $plus_status = PlusTransaction::where('user_id', $user->id)->where('opportunity_id', $opportunity->id)->select('status');
                
                $check = Validator::make($request->all(), [
                    'content' => 'required|string',
                    'comment_id' => 'required|string',
                    'user_name' => 'required|string',
                    ]);

                if ($check->fails()) {
                    return $apiResponse->sendResponse(400, 'Bad Request', $check->errors());
                }

                $data = new RecordReply;
                $data->content = $request->content;
                $data->comment_id = $request->comment_id;
                $data->user_id = $user->id;
                $data->user_name = $request->user_name;
                $data->save();
                $response_data["message"] = "Comment recorded";
                return $this->apiResponse->sendResponse(200,'Success',$response_data);
            };
            

        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(404,'Page Not Found',null);
        }

    }

    public function show_comment($opportunity_id)
    {

    }
}
