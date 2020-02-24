<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiResponse;
use App\Language;
use App\Opportunity;
use App\PlusTransaction;
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
        $this->KEY = env('SYS_API_KEY');
     }
     
    public function save_comment(Request $request)
    {
        try {            
            $flag = 0;
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                $flag = $this->txnflag->check_subscription(Auth::user()->id);
                $plus_status = PlusTransaction::where('user_id', $user->id)->where('opportunity_id', $opportunity->id)->select('status');
                
                $check = Validator::make($request->all(), [
                    'token' => 'required|string',
                    'message' => 'required|string',
                    'opportunity_id' => 'required|string',
                    ]);

                if ($check->fails()) {
                    return $apiResponse->sendResponse(400, 'Bad Request', $check->errors());
                }
                if ($request->token != $this->KEY) {
                    return $apiResponse->sendResponse(401, 'Unauthorized Request', '');
                }

                $data = new ListComments;
                $data->message = $request->message;
                $data->save();

                $comment_id = DB::table('list_comments')->orderBy('id', 'DESC')->first()->id;

                $data = new UserComments;
                $data->user_id = $user->id;
                $data->comment_id = $comment_id;
                $data->save();

                $data = new OpportunityComments;
                $data->opportunity_id = $request->opportunity_id;
                $data->comment_id = $comment_id;
                $data->save();

                return $this->apiResponse->sendResponse(200,'Success','Recorded');
            };
            

        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(404,'Page Not Found','Error');
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
                    'token' => 'required|string',
                    'content' => 'required|string',
                    'comment_id' => 'required|string',
                    'user_name' => 'required|string',
                    ]);

                if ($check->fails()) {
                    return $apiResponse->sendResponse(400, 'Bad Request', $check->errors());
                }
                if ($request->token != $this->KEY) {
                    return $apiResponse->sendResponse(401, 'Unauthorized Request', '');
                }

                $data = new RecordReply;
                $data->content = $request->content;
                $data->comment_id = $request->comment_id;
                $data->user_id = $user->id;
                $data->user_name = $request->user_name;
                $data->save();

                return $this->apiResponse->sendResponse(200,'Success','Recorded');
            };
            

        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(404,'Page Not Found','Error');
        }

    }

    public function show_comment($opportunity_id)
    {

    }
}
