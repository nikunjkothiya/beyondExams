<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Http\Controllers\SubscriptionController;
use App\Language;
use App\UserDetail;
use App\User;
use App\Country;
use App\Discipline;
use App\Qualification;
use App\TagType;
use App\Tag;
use App\Opportunity;

class PageController extends Controller
{
    protected $languages;
    protected $txnflag;

    public function __construct(){
        try{
            $this->languages = Language::all();
            $this->txnflag = new SubscriptionController;
        }
        catch(Exception $e){

        }
    }

    public function index(){
    	return redirect('/login');
    }

    public function setup($id){
    	try{
            $check = UserDetail::where('user_id',Auth::user()->id)->first();
            if($check){
                return redirect('dashboard');
            }
            else{
                if($id==1){
                    return view('pages.setup1',['languages'=>$this->languages,'txnflag'=>$this->txnflag->check_subscription(Auth::user()->id)]);
                }
                elseif($id==2){
                    $countries = Country::all();
                    $disciplines = Discipline::all();
                    $qualifications = Qualification::all();
                    return view('pages.setup2',['languages'=>$this->languages,'countries'=>$countries,'disciplines'=>$disciplines,'qualifications'=>$qualifications,'txnflag'=>$this->txnflag->check_subscription(Auth::user()->id)]);
                }
                else{

                }
            }
    		
    	}
    	catch(Exception $e){
    		return redirect('/login');
    	}
    }

    public function setup_details(Request $request){
        try{
            $validator = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'college' => 'required|string|max:1024',
                'gpa' => 'required|numeric|between:0,10.00',
                'qualification' => 'required|integer|min:1|max:'.Qualification::count(),
                'discipline' => 'required|integer|min:1|max:'.Discipline::count(),
                'city' => 'required|string|max:255',
                'country' => 'required|integer|min:1|max:'.Country::count(),
                'email' => 'required|email',
            ]);
            $check = UserDetail::where('user_id',Auth::user()->id)->first();
            if(is_null($check)){
                $record = new UserDetail;
                $record->user_id = Auth::user()->id;
                $record->language_id = Language::where('code',\Config::get('app.locale'))->first()->id;
                $record->email = $request->email;
                $record->firstname = $request->firstname;
                $record->lastname = $request->lastname;
                $record->college = $request->college;
                $record->city = $request->city;
                $record->gpa = $request->gpa;
                $record->qualification_id = $request->qualification;
                $record->discipline_id = $request->discipline;
                $record->country_id = $request->country;
                $record->save();
            }
            else{
                return redirect('/dashboard');
            }
            if($record){
                $user = Auth::user();
                if($user->tags->isEmpty())
                    return redirect('dashboard/filter');
                else
                    return redirect('dashboard');
            }
            else{
                return redirect()->back()->withErrors(['Error', 'Internal Server Error 500.']);
            }
            
        }
        catch(Exception $e){

        }
    }

    public function dashboard(){
        try{
            $pcheck = UserDetail::where('user_id',Auth::user()->id)->first();
            $user = User::find(Auth::user()->id);
            if($user->tags->isEmpty()){
                return redirect('/dashboard/filter');
            }

            $filter = array();
            $uts = $user->tags;
            foreach ($uts as $ut) {
                $filter[]=$ut->id;
            }
            $opportunities = Opportunity::with('tags')->whereHas('tags',function($q) use ($filter) {
                $q->whereIn('id', $filter);
            })->whereDate('deadline','>=',Carbon::today()->toDateString())->orderBy('deadline','ASC')->paginate(1);
            
        }
        catch(Exception $e){

        }
        
        return view('pages.dashboard',['languages'=>$this->languages,'pcheck'=>$pcheck,'opportunities'=>$opportunities,'txnflag'=>$this->txnflag->check_subscription(Auth::user()->id)]);
    }

    public function profile(){
        try{
            $pcheck = UserDetail::where('user_id',Auth::user()->id)->first();
        }
        catch(Exception $e){

        }
        if($pcheck){
            $countries = Country::all();
            $disciplines = Discipline::all();
            $qualifications = Qualification::all();
            return view('pages.profile',['languages'=>$this->languages,'pcheck'=>$pcheck,'countries'=>$countries,'disciplines'=>$disciplines,'qualifications'=>$qualifications,'txnflag'=>$this->txnflag->check_subscription(Auth::user()->id)]);    
        }
        return redirect('/setup/2');
        
    }

    public function filter(){
        try{
            $tags = TagType::find(1)->tags;
            $disciplines = TagType::find(3)->tags;
            $qualifications = TagType::find(2)->tags;

            $user = Auth::user();
            $utags = $user->tags;
            $utid = array();
            foreach ($utags as $utag){
                $utid[] = $utag->id;
            }
        }
        catch(Exception $e){

        }
        return view('pages.filter',['languages'=>$this->languages,'tags'=>$tags,'disciplines'=>$disciplines,'qualifications'=>$qualifications,'utid'=>$utid,'txnflag'=>$this->txnflag->check_subscription(Auth::user()->id)]);
    }

    public function save_filter(Request $request){
        try{
            $user = Auth::user();
            $tags = $request->tags;
            if(empty($tags)){
                return redirect()->back()->withErrors(['Select atlest one category']);
            }
            if(!empty($tags)){
                $user->tags()->sync($tags);   
            }
        }
        catch(Exception $e){

        }
        return redirect('dashboard');
    }

    public function save_opp(Request $request){
        try{
            $user = User::find(Auth::user()->id);
            if($user->tags->isEmpty()){
                return redirect('/dashboard/filter');
            }
            $pcheck = UserDetail::where('user_id',Auth::user()->id)->first();
            $oppids = array();
            $opps = $user->saved_opportunities;
            foreach ($opps as $opp) {
                $oppids[] = $opp->id;
            }
            $opportunities = Opportunity::whereIn('id',$oppids)->orderBy('deadline','ASC')->paginate(1);
        }
        catch(Exception $e){

        }
        return view('pages.saved-opp',['languages'=>$this->languages,'pcheck'=>$pcheck,'opportunities'=>$opportunities,'txnflag'=>$this->txnflag->check_subscription(Auth::user()->id)]);
    }

    public function message(Request $request){
        try{
            $pcheck = UserDetail::where('user_id',Auth::user()->id)->first();
        }
        catch(Exception $e){

        }
        return view('pages.message',['languages'=>$this->languages,'pcheck'=>$pcheck,'txnflag'=>$this->txnflag->check_subscription(Auth::user()->id)]);
    }

    public function save_profile(Request $request){
        try{
            $validator = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'college' => 'required|string|max:1024',
                'gpa' => 'required|numeric|between:0,10.00',
                'qualification' => 'required|integer|min:1|max:'.Qualification::count(),
                'discipline' => 'required|integer|min:1|max:'.Discipline::count(),
                'city' => 'required|string|max:255',
                'country' => 'required|integer|min:1|max:'.Country::count(),
                'email' => 'required|email',
            ]);
            UserDetail::where('user_id',Auth::user()->id)->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'college' => $request->college,
                'gpa' => $request->gpa,
                'qualification_id' => $request->qualification,
                'discipline_id' => $request->discipline,
                'city' => $request->city,
                'country_id' => $request->country,
                'email' => $request->email,
            ]);
            $saved = 1;
            return redirect('/dashboard/profile')->with(compact('saved'));
        }
        catch(Exception $e){

        }
    }
}
