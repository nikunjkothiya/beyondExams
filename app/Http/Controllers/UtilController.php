<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiResponse;

use App\Language;
use App\Opportunity;
use App\OpportunityLocation;
use App\User;
use App\FundType;
use App\Tag;
use App\EligibleRegion;

class UtilController extends Controller
{
    protected $code = array();

    public function __construct(){
        $languages = Language::all();
        foreach($languages as $lang){
        	$this->code[] = $lang->code;
        }
        $this->KEY = env('SYS_API_KEY');
    }

    public function locale($locale){
    	if(in_array($locale, $this->code)){
    		\App::setLocale($locale);
            session()->put('locale', $locale);
        	return redirect()->back();
    	}
    	else{
    		\App::setLocale('en');
            session()->put('locale', 'en');
        	return redirect()->back();	
    	}
    }

    public function save_opportunity(Request $request){
        try{
            $validator = $request->validate([
                'id' => 'required|exists:opportunities',
            ]);
            $id = $request->id;
            $user = Auth::user();
            $user->saved_opportunities()->detach($id);
            $user->saved_opportunities()->attach($id);
        }
        catch(Exception $e){

        }
        return response()->json([
            'status' => 'success',
            'status_code' => '200',
            'message' => 'Opportunity Saved',
            'data' => ''
        ]);
    }

    public function unsave_opportunity(Request $request){
        try{
            $validator = $request->validate([
                'id' => 'required|exists:opportunities',
            ]);
            $id = $request->id;
            $user = Auth::user();
            $user->saved_opportunities()->detach($id);
        }
        catch(Exception $e){

        }
        return response()->json([
            'status' => 'success',
            'status_code' => '200',
            'message' => 'Removed Opportunity',
            'data' => ''
        ]);
    }

    public function next_opps(){
        $user = User::find(Auth::user()->id);
        $filter = array();
        $uts = $user->tags;
        foreach ($uts as $ut) {
            $filter[]=$ut->id;
        }
        $opportunities = Opportunity::with('tags')->whereHas('tags',function($q) use ($filter) {
            $q->whereIn('id', $filter);
        })->whereDate('deadline','>=',Carbon::today()->toDateString())->orderBy('deadline','ASC')->paginate(1);
        return response()->json($opportunities);
    }

    public function prev_opps(){
        $user = User::find(Auth::user()->id);
        $filter = array();
        $uts = $user->tags;
        foreach ($uts as $ut) {
            $filter[]=$ut->id;
        }
        $opportunities = Opportunity::with('tags')->whereHas('tags',function($q) use ($filter) {
            $q->whereIn('id', $filter);
        })->whereDate('deadline','>=',Carbon::today()->toDateString())->orderBy('deadline','ASC')->paginate(1);
        return response()->json($opportunities);
    }

    public function next_saved_opps(){
        $user = User::find(Auth::user()->id);
        $oppids = array();
        $opps = $user->saved_opportunities;
        foreach ($opps as $opp) {
            $oppids[] = $opp->id;
        }
        $opportunities = Opportunity::with('tags')->whereIn('id',$oppids)->orderBy('deadline','ASC')->paginate(1);
        return response()->json($opportunities);
    }

    public function post_opportunity(Request $request){
        try{

            $apiResponse = new ApiResponse;
            $check = Validator::make($request->all(),[
                'token' => 'required|string',
                'deadline' => 'required|date',
                'image' => 'required|string',
                'link' => 'required|string',
                'fund_type' => 'required|integer|min:1|max:'.FundType::count(),
                'opportunity_location' => 'required|integer|min:1|max:'.OpportunityLocation::count(),

                'bn.title' => 'required|string',
                'bn.description' => 'required|string',
                'de.title' => 'required|string',
                'de.description' => 'required|string',
                'en.title' => 'required|string',
                'en.description' => 'required|string',
                'es.title' => 'required|string',
                'es.description' => 'required|string',
                'fr.title' => 'required|string',
                'fr.description' => 'required|string',
                'hi.title' => 'required|string',
                'hi.description' => 'required|string',
                'id.title' => 'required|string',
                'id.description' => 'required|string',
                'it.title' => 'required|string',
                'it.description' => 'required|string',
                'ja.title' => 'required|string',
                'ja.description' => 'required|string',
                'km.title' => 'required|string',
                'km.description' => 'required|string',
                'ko.title' => 'required|string',
                'ko.description' => 'required|string',
                'lo.title' => 'required|string',
                'lo.description' => 'required|string',
                'ms.title' => 'required|string',
                'ms.description' => 'required|string',
                'my.title' => 'required|string',
                'my.description' => 'required|string',
                'ne.title' => 'required|string',
                'ne.description' => 'required|string',
                'ro.title' => 'required|string',
                'ro.description' => 'required|string',
                'ru.title' => 'required|string',
                'ru.description' => 'required|string',
                'si.title' => 'required|string',
                'si.description' => 'required|string',
                'ta.title' => 'required|string',
                'ta.description' => 'required|string',
                'th.title' => 'required|string',
                'th.description' => 'required|string',
                'tl.title' => 'required|string',
                'tl.description' => 'required|string',
                'vi.title' => 'required|string',
                'vi.description' => 'required|string',
                'zh.title' => 'required|string',
                'zh.description' => 'required|string',

                'tags' => 'required|array|min:1',
                'tags.*' => 'integer|min:1|max:'.Tag::count(),
                'eligible_regions' => 'required|array|min:1',
                'eligible_regions.*' => 'integer|min:1|max:'.EligibleRegion::count(),
            ]);
            if($check->fails()){
                return $apiResponse->sendResponse(400,'Bad Request',$check->errors());
            }
            if($request->token != $this->KEY){
                return $apiResponse->sendResponse(401,'Unauthorized Request','');      
            }
            $slug = str_replace(" ", "-", strtolower($request->en['title']))."-".substr(hash('sha256', mt_rand() . microtime()), 0, 16);
            $opportunity = array(
                'deadline' => $request->deadline,
                'image' => $request->image,
                'link' => $request->link,
                'fund_type_id' => $request->fund_type,
                'slug' => $slug,
                'opportunity_location_id' => $request->opportunity_location,
                'bn' => [
                    'title' => $request->bn['title'],
                    'description' => $request->bn['description'],
                ],
                'de' => [
                    'title' => $request->de['title'],
                    'description' => $request->de['description'],
                ],
                'en' => [
                    'title' => $request->en['title'],
                    'description' => $request->en['description'],
                ],
                'es' => [
                    'title' => $request->es['title'],
                    'description' => $request->es['description'],
                ],
                'fr' => [
                    'title' => $request->fr['title'],
                    'description' => $request->fr['description'],
                ],
                'hi' => [
                    'title' => $request->hi['title'],
                    'description' => $request->hi['description'],
                ],
                'id' => [
                    'title' => $request->id['title'],
                    'description' => $request->id['description'],
                ],
                'it' => [
                    'title' => $request->it['title'],
                    'description' => $request->it['description'],
                ],
                'ja' => [
                    'title' => $request->ja['title'],
                    'description' => $request->ja['description'],
                ],
                'km' => [
                    'title' => $request->km['title'],
                    'description' => $request->km['description'],
                ],
                'ko' => [
                    'title' => $request->ko['title'],
                    'description' => $request->ko['description'],
                ],
                'lo' => [
                    'title' => $request->lo['title'],
                    'description' => $request->lo['description'],
                ],
                'ms' => [
                    'title' => $request->ms['title'],
                    'description' => $request->ms['description'],
                ],
                'my' => [
                    'title' => $request->my['title'],
                    'description' => $request->my['description'],
                ],
                'ne' => [
                    'title' => $request->ne['title'],
                    'description' => $request->ne['description'],
                ],
                'ro' => [
                    'title' => $request->ro['title'],
                    'description' => $request->ro['description'],
                ],
                'ru' => [
                    'title' => $request->ru['title'],
                    'description' => $request->ru['description'],
                ],
                'si' => [
                    'title' => $request->si['title'],
                    'description' => $request->si['description'],
                ],
                'ta' => [
                    'title' => $request->ta['title'],
                    'description' => $request->ta['description'],
                ],
                'th' => [
                    'title' => $request->th['title'],
                    'description' => $request->th['description'],
                ],
                'tl' => [
                    'title' => $request->tl['title'],
                    'description' => $request->tl['description'],
                ],
                'vi' => [
                    'title' => $request->vi['title'],
                    'description' => $request->vi['description'],
                ],
                'zh' => [
                    'title' => $request->zh['title'],
                    'description' => $request->zh['description'],
                ],
            );

            $r = Opportunity::create($opportunity);
            $r->tags()->sync($request->tags); 
            $r->eligible_regions()->sync($request->eligible_regions);
            
            $data = [
                "id" => $r->id,
            ];

            return $apiResponse->sendResponse(200,'Opportunity Successfully Inserted',$data);
        }
        catch(Exception $e){
            return $apiResponse->sendResponse(500,'Internal Server Error','');
        }
    }
}
