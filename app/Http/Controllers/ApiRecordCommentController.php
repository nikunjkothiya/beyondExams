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
                
                $check = Validator::make($request->all(), [
                    'content' => 'required|string',
                    'comment_id' => 'required|string',
                    'user_name' => 'required|string',
                    ]);

                if ($check->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Bad Request', $check->errors());
                }

                $data = new RecordReply;
                $data->content = $request->content;
                $data->comment_id = $request->comment_id;
                $data->user_id = $user->id;
                $data->user_name = $request->user_name;
                $data->save();
                $response_data["message"] = "Reply recorded";
                return $this->apiResponse->sendResponse(200,'Success',$response_data);
            };
            

        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(404,'Page Not Found',null);
        }

    }

    public function show_comment(Request $request)
    {   
        $check = Validator::make($request->all(), [
            'opportunity_id' => 'required',
            ]);

        if ($check->fails()) {
            return $this->apiResponse->sendResponse(400, 'Bad Request', $check->errors());
        }

        $comment_id = DB::table('opportunity_comments')->select('comment_id')->where("opportunity_id",$request->opportunity_id)->orderby('updated_at')->get();
        $comm_ids = array();
        foreach ($comment_id as $id){$comm_ids[] = $id->comment_id;}
        if(empty($comm_ids)){return $this->apiResponse->sendResponse(200,'No Comment',null);}

        $comments = DB::table('list_comments')->select('id', 'message', 'created_at')->whereIn("id",$comm_ids)->get();
        $replies  = DB::table('reply')->select('content')->where("comment_id",$comm_ids)->first();
        
        $reply_flag = 0;

        if($replies==null){$reply_flag==1;}

        if($reply_flag==1){
            $data = [];
            foreach ($comments as $comm){
                $user_id  = DB::table('user_comments')->select('user_id')->where("comment_id",$comm->id)->first();
                $comment_user = DB::table('users')->select('name')->where("id",$user_id->user_id)->first();
                $data[] = array('id'=>$comm->id, 'user'=>$comment_user->name, 'comment'=>$comm->message, 'comment_date'=>$comm->created_at, 'reply'=>null);
             }
        }
        else{
            $data=[];
            $i = 0;
            foreach ($comments as $comm){
                $flag = 0;
                $rep = DB::table('reply')->select('content', 'created_at', 'user_id')->where("comment_id",$comm_ids[$i])->get();
                $user_id  = DB::table('user_comments')->select('user_id')->where("comment_id",$comm_ids[$i])->first();
                $comment_user = DB::table('users')->select('name')->where("id",$user_id->user_id)->first();
                if($rep==null){$flag = 1;}

                if($flag==0)
                {   $data_json = array('id'=>$comm->id, 'comment_user'=>$comment_user->name, 'comment'=>$comm->message, 'comment_date'=>$comm->created_at, 'reply_data'=> null);
                    $reply_json = [];
                    foreach($rep as $r){
                        $user = DB::table('users')->select('name')->where("id",$r->user_id)->first();
                        $reply_json[] = array('reply'=>$r->content, 'reply_date'=>$r->created_at, 'reply_user'=>$user->name);    
                    }
                    $data_json['reply_data'] = $reply_json;
                    $data[] = $data_json;
                }
                else{
                    $data[] = array('id'=>$comm->id, 'comment_user'=>$comment_user->name, 'comment'=>$comm->message, 'reply'=>null);}
                $i=$i+1;
             }
        }
        
        return $this->apiResponse->sendResponse(200,'Success',$data);

    }
}
