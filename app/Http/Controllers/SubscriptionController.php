<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

use App\Language;
use App\UserDetail;

class SubscriptionController extends Controller
{
    protected $languages;

    public function __construct(){
        try{
            $this->languages = Language::all();
        }
        catch(Exception $e){

        }
    }

    public function subscription(Request $request){
        try{
            $pcheck = UserDetail::where('user_id',Auth::user()->id)->first();
        }
        catch(Exception $e){
            
        }
        return view('pages.subscription',['languages'=>$this->languages,'pcheck'=>$pcheck]);
    }
}
