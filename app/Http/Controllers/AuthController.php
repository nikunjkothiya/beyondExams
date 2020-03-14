<?php

namespace App\Http\Controllers;

use Auth;
use Socialite;

use Illuminate\Http\Request;
use App\Language;
use App\UserDetail;
use App\UserSocial;
use App\User;

class AuthController extends Controller
{
    protected $languages;
    protected $providers = ['facebook','google'];

    public function __construct(){
        $this->languages = Language::all();
    }

    public function login(){
        if(Auth::check()){
            return redirect('/setup/1');    
        }
    	return view('login',['languages'=>$this->languages]);
    }
    
    public function logout(){
        if(Auth::check()){
            Auth::logout();    
        }
        return redirect('/login');
    }

    public function redirect($provider){
    	if(in_array($provider, $this->providers)){
    		return Socialite::driver($provider)->redirect();	
    	}
    	else{
    		return redirect('/login');
    	}
    }

    public function callback($provider){
    	try{
    		$user = Socialite::driver($provider)->stateless()->user();
    	}
    	catch(Exception $e){
    		return redirect('login');
    	}
    	if ($authUser = UserSocial::where('provider_id', $user->id)->first()) {
            $temp_user = User::where('email',$user->email)->first();
            Auth::login($temp_user, true);
        	return redirect('setup/1');
       	}
        elseif($fuser = User::where('email',$user->email)->first()){
            return redirect('/login')->with('email', 'User email already registered!');
        }
        else{
            $new_user = new User();
            $new_user->name = $user->name;
            $new_user->email = $user->email;
            $new_user->avatar = $user->avatar;
            $new_user->save();
            $new_user->social_accounts()->create(
                ['provider_id' => $user->id, 'provider' => $provider]
            );
            Auth::login($new_user, true);
            return redirect('setup/1');
        }
    }
}
