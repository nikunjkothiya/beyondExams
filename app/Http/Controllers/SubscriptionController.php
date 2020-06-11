<?php

namespace App\Http\Controllers;
use App\Http\Controllers\ApiResponse;
use Illuminate\Http\Request;

use Auth;

use App\Language;
use App\UserDetail;
use App\Product;
use App\Transaction;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    protected $languages;
    protected $key;
    protected $salt;
    protected $url;
    protected $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
    protected $data = array();

    public function __construct(ApiResponse $apo){
        try{
            $this->languages = Language::all();
            $this->key = env('PAYU_MERCHANT_KEY');
            $this->salt = env('PAYU_MERCHANT_SALT');
            $this->apiResponse=$apo;
            if(env('APP_DEBUG') == 'true'){
                $this->url = env('PAYU_TEST_URL');
            }
            else{
                $this->url = env('PAYU_LIVE_URL');   
            }
        }
        catch(Exception $e){

        }
    }

    public function check_subscription($user_id){
        try{
            $check = Transaction::where('user_id',$user_id)->where('valid',1)->orderBy('datetime','DESC')->first();
            if(is_null($check)){
                return 0;    
            }
            $days = $check->product->months*30;
            $current = Carbon::now();
            $ddays = $current->diffInDays(Carbon::parse($check->datetime));
            if($ddays <= $days){
                return $days-$ddays;
            }
            else{
                return 0;
            }
        }
        catch(Exception $e){

        }
    }

    public function subscription(Request $request){
        try{
            $pcheck = UserDetail::where('user_id',Auth::user()->id)->first();
            $check = Transaction::where('product_id',1)->where('valid',1)->where('user_id',Auth::user()->id)->first();
            if(is_null($check)){
                $plans = Product::where('enable',1)->get();
            }
            else{
                $plans = Product::where('enable',1)->where('id','>',1)->get();
            }
            if(is_null($pcheck)){
                $firstname = Auth::user()->name;
                $email = Auth::user()->email;        
            }
            else{
                $firstname = $pcheck->firstname;
                $email = $pcheck->email;
            }
            $txnflag = $this->check_subscription(Auth::user()->id);
        }
        catch(Exception $e){
            return $this->apiResponse->sendResponse(500,'Internal Server Error',$e);
        }
        #'languages'=>$this->languages,'pcheck'=>$pcheck,
        $data = ['plans'=>$plans,'txnflag'=>$txnflag,'firstname'=>$firstname,'email'=>$email];
        #return view('pages.subscription',$data);
        return $this->apiResponse->sendResponse(200,'Success',$data);
    }

    public function checkout(Request $request){
        try{
            $validator = $request->validate([
                'plans' => 'required|min:1|max:'.Product::count(),
                'firstname' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string|max:10|min:10',
            ]);
            if($request->plans == 1){
                $check = Transaction::where('product_id',1)->where('valid',1)->where('user_id',Auth::user()->id)->first();
                if(is_null($check)){
                    $txnid = $this->generateTransactionID();
                    $buy = new Transaction;
                    $buy->transaction_id = $txnid;
                    $buy->user_id = Auth::user()->id;
                    $buy->product_id = 1;
                    $buy->datetime = Carbon::today()->toDateString();
                    $buy->valid = 1;
                    $buy->save();

                    return redirect('/dashboard/subscription');
                    #return $this->apiResponse->sendResponse(200,'Success','redirect to /dashboard/subscription');
                }
                else{
                    return redirect()->back()->withErrors(['Free Trial Exhausted!']);
                    #return $this->apiResponse->sendResponse(401,'Unauthorized','Free Trial Exhausted!');
                }
            }
            else{
                $this->data['key'] = $this->key;
                $this->data['txnid'] = $this->generateTransactionID();
                $this->data['amount'] = Product::find($request->plans)->price;
                $this->data['productinfo'] = $request->plans;
                $this->data['firstname'] = $request->firstname;
                $this->data['email'] = $request->email;
                $this->data['udf1'] = Auth::user()->id;
                $this->data['phone'] = $request->phone;
                $this->data['service_provider'] = "payu_paisa";
                $this->data['surl'] = url('/success');
                $this->data['furl'] = url('/failure');
                $hash = $this->generateHash();

                $buy = new Transaction;
                $buy->transaction_id = $this->data['txnid'];
                $buy->user_id = Auth::user()->id;
                $buy->product_id = $request->plans;
                $buy->datetime = Carbon::today()->toDateString();
                $buy->save();

            }
        }
        catch(Exception $e){

        }
        $data = ['endPoint'=>$this->url,'hash'=>$hash,'parameters'=>$this->data];
        return view('pages.payumoney',$data);
        #return $this->apiResponse->sendResponse(200,'to payumoney',$data);
    }

    public function success(Request $request){
        try{
            if($request->key == $this->key){
                Transaction::where('transaction_id',$request->txnid)->update(['valid'=>1]);
            }
        }
        catch(Exception $e){

        }
        return redirect('/dashboard/subscription');
        #return $this->apiResponse->sendResponse(200,'Success','redirect to /dashboard/subscription');
    }

    public function failure(Request $request){
        try{
            if($request->key == $this->key){

            }
        }
        catch(Exception $e){

        }
        return redirect('/dashboard/subscription');
        #return $this->apiResponse->sendResponse(500,'Failed','redirect to /dashboard/subscription');
    }

    public function generateTransactionID(){
        return substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    }

    public function generateHash(){
        $hashVarsSeq = explode('|', $this->hashSequence);
        $hash_string = '';  
        foreach($hashVarsSeq as $hash_var) {
            $hash_string .= isset($this->data[$hash_var]) ? $this->data[$hash_var] : '';
            $hash_string .= '|';
        }
        $hash_string .= $this->salt;
        $hash = strtolower(hash('sha512', $hash_string));
        return $hash;
    }
}
