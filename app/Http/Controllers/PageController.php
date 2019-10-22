<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Language;
use App\UserDetail;
use App\User;
use App\Country;
use App\Discipline;
use App\Qualification;
use App\Tag;
use App\Opportunity;

class PageController extends Controller
{
    protected $languages;

    public function __construct(){
        try{
            $this->languages = Language::all();
        }
        catch(Exception $e){

        }
    }

    public function index(){
    	return view('welcome',['languages'=>$this->languages]);
    }

    public function setup($id){
    	try{
            $check = UserDetail::where('user_id',Auth::user()->id)->first();
            if($check){
                return redirect('dashboard');
            }
            else{
                if($id==1){
                    return view('pages.setup1',['languages'=>$this->languages]);
                }
                elseif($id==2){
                    $countries = Country::all();
                    $disciplines = Discipline::all();
                    $qualifications = Qualification::all();
                    return view('pages.setup2',['languages'=>$this->languages,'countries'=>$countries,'disciplines'=>$disciplines,'qualifications'=>$qualifications]);
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
            ]);
            $check = UserDetail::where('user_id',Auth::user()->id)->first();
            if(is_null($check)){
                $record = new UserDetail;
                $record->user_id = Auth::user()->id;
                $record->language_id = Language::where('code',\Config::get('app.locale'))->first()->id;
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
                $utags = $user->tags;
                $udisciplines = $user->disciplines;
                $uqualifications = $user->qualifications;
                if($utags->isEmpty() && $udisciplines->isEmpty() && $uqualifications->isEmpty())
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
            $user = User::find(Auth::user()->id);
            $pcheck = UserDetail::where('user_id',Auth::user()->id)->first();
            $tfilter = array();
            $uts = Auth::user()->tags;
            foreach ($uts as $ut) {
                $tfilter[]=$ut->id;
            }
            
        }
        catch(Exception $e){

        }
        if($user->tags->isEmpty() && $user->disciplines->isEmpty() && $user->qualifications->isEmpty()){
            return redirect('/dashboard/filter');
        }
        return view('pages.dashboard',['languages'=>$this->languages,'pcheck'=>$pcheck]);
    }

    public function profile(){
        try{
            $pcheck = UserDetail::where('user_id',Auth::user()->id)->first();
        }
        catch(Exception $e){

        }
        if($pcheck){
            return view('pages.profile',['languages'=>$this->languages,'pcheck'=>$pcheck]);    
        }
        return redirect('/setup/2');
        
    }

    public function filter(){
        try{
            $tags = Tag::all();
            $disciplines = Discipline::all();
            $qualifications = Qualification::all();

            $user = Auth::user();
            $utags = $user->tags;
            $utid = array();
            foreach ($utags as $utag){
                $utid[] = $utag->id;
            }
            $udisciplines = $user->disciplines;
            $udid = array();
            foreach ($udisciplines as $udiscipline) {
                $udid[] = $udiscipline->id; 
            }
            $uqualifications = $user->qualifications;
            $uqid = array();
            foreach ($uqualifications as $uqualification) {
                $uqid[] = $uqualification->id; 
            }
        }
        catch(Exception $e){

        }
        return view('pages.filter',['languages'=>$this->languages,'tags'=>$tags,'disciplines'=>$disciplines,'qualifications'=>$qualifications,'utid'=>$utid,'udid'=>$udid,'uqid'=>$uqid]);
    }

    public function save_filter(Request $request){
        try{
            $user = Auth::user();
            $tags = $request->tags;
            $qualifications = $request->qualifications;
            $disciplines = $request->disciplines;
            if(empty($tags) && empty($qualifications) && empty($disciplines)){
                return redirect()->back()->withErrors(['Select atlest one category']);
            }
            if(!empty($tags)){
                $user->tags()->sync($tags);   
            }
            if(!empty($qualifications)){
                $user->qualifications()->sync($qualifications);   
            }
            if(!empty($disciplines)){
                $user->disciplines()->sync($disciplines);   
            }
        }
        catch(Exception $e){

        }
        return redirect('dashboard');
    }

    public function save_opp(Request $request){
        try{
            $pcheck = UserDetail::where('user_id',Auth::user()->id)->first();
        }
        catch(Exception $e){

        }
        return view('pages.saved-opp',['languages'=>$this->languages,'pcheck'=>$pcheck]);
    }

    public function support(Request $request){
        try{
            $pcheck = UserDetail::where('user_id',Auth::user()->id)->first();
        }
        catch(Exception $e){

        }
        return view('pages.support',['languages'=>$this->languages,'pcheck'=>$pcheck]);
    }
}
