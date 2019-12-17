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

                'bn.title' => 'string',
                'bn.description' => 'string',
                'de.title' => 'string',
                'de.description' => 'string',
                'en.title' => 'string',
                'en.description' => 'string',
                'es.title' => 'string',
                'es.description' => 'string',
                'fr.title' => 'string',
                'fr.description' => 'string',
                'hi.title' => 'string',
                'hi.description' => 'string',
                'id.title' => 'string',
                'id.description' => 'string',
                'it.title' => 'string',
                'it.description' => 'string',
                'ja.title' => 'string',
                'ja.description' => 'string',
                'km.title' => 'string',
                'km.description' => 'string',
                'ko.title' => 'string',
                'ko.description' => 'string',
                'lo.title' => 'string',
                'lo.description' => 'string',
                'ms.title' => 'string',
                'ms.description' => 'string',
                'my.title' => 'string',
                'my.description' => 'string',
                'ne.title' => 'string',
                'ne.description' => 'string',
                'ro.title' => 'string',
                'ro.description' => 'string',
                'ru.title' => 'string',
                'ru.description' => 'string',
                'si.title' => 'string',
                'si.description' => 'string',
                'ta.title' => 'string',
                'ta.description' => 'string',
                'th.title' => 'string',
                'th.description' => 'string',
                'tl.title' => 'string',
                'tl.description' => 'string',
                'vi.title' => 'string',
                'vi.description' => 'string',
                'zh.title' => 'string',
                'zh.description' => 'string',

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
                /*'bn' => [
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
                ],*/
            );
//dd(!is_null($request->bn));
            if(!is_null($request->bn)){
                $opportunity['bn'] = [
                    'title' => $request->bn['title'],
                    'description' => $request->bn['description'],
                ];
            }
            if(!is_null($request->de)){
                $opportunity['de'] = [
                    'title' => $request->de['title'],
                    'description' => $request->de['description'],
                ];
            }
            if(!is_null($request->en)){
                $opportunity['en'] = [
                    'title' => $request->en['title'],
                    'description' => $request->en['description'],
                ];
            }
            if(!is_null($request->es)){
                $opportunity['es'] = [
                    'title' => $request->es['title'],
                    'description' => $request->es['description'],
                ];
            }
            if(!is_null($request->fr)){
                $opportunity['fr'] = [
                    'title' => $request->fr['title'],
                    'description' => $request->fr['description'],
                ];
            }
            if(!is_null($request->hi)){
                $opportunity['hi'] = [
                    'title' => $request->hi['title'],
                    'description' => $request->hi['description'],
                ];
            }
            if(!is_null($request->id)){
                $opportunity['id'] = [
                    'title' => $request->id['title'],
                    'description' => $request->id['description'],
                ];
            }
            if(!is_null($request->it)){
                $opportunity['it'] = [
                    'title' => $request->it['title'],
                    'description' => $request->it['description'],
                ];
            }
            if(!is_null($request->ja)){
                $opportunity['ja'] = [
                    'title' => $request->ja['title'],
                    'description' => $request->ja['description'],
                ];
            }
            if(!is_null($request->km)){
                $opportunity['km'] = [
                    'title' => $request->km['title'],
                    'description' => $request->km['description'],
                ];
            }
            if(!is_null($request->ko)){
                $opportunity['ko'] = [
                    'title' => $request->ko['title'],
                    'description' => $request->ko['description'],
                ];
            }
            if(!is_null($request->lo)){
                $opportunity['lo'] = [
                    'title' => $request->lo['title'],
                    'description' => $request->lo['description'],
                ];
            }
            if(!is_null($request->ms)){
                $opportunity['ms'] = [
                    'title' => $request->ms['title'],
                    'description' => $request->ms['description'],
                ];
            }
            if(!is_null($request->my)){
                $opportunity['my'] = [
                    'title' => $request->my['title'],
                    'description' => $request->my['description'],
                ];
            }
            if(!is_null($request->ne)){
                $opportunity['ne'] = [
                    'title' => $request->ne['title'],
                    'description' => $request->ne['description'],
                ];
            }
            if(!is_null($request->ro)){
                $opportunity['ro'] = [
                    'title' => $request->ro['title'],
                    'description' => $request->ro['description'],
                ];
            }
            if(!is_null($request->ru)){
                $opportunity['ru'] = [
                    'title' => $request->ru['title'],
                    'description' => $request->ru['description'],
                ];
            }
            if(!is_null($request->si)){
                $opportunity['si'] = [
                    'title' => $request->si['title'],
                    'description' => $request->si['description'],
                ];
            }
            if(!is_null($request->ta)){
                $opportunity['ta'] = [
                    'title' => $request->ta['title'],
                    'description' => $request->ta['description'],
                ];
            }
            if(!is_null($request->th)){
                $opportunity['th'] = [
                    'title' => $request->th['title'],
                    'description' => $request->th['description'],
                ];
            }
            if(!is_null($request->tl)){
                $opportunity['tl'] = [
                    'title' => $request->tl['title'],
                    'description' => $request->tl['description'],
                ];
            }
            if(!is_null($request->vi)){
                $opportunity['vi'] = [
                    'title' => $request->vi['title'],
                    'description' => $request->vi['description'],
                ];
            }
            if(!is_null($request->zh)){
                $opportunity['zh'] = [
                    'title' => $request->zh['title'],
                    'description' => $request->zh['description'],
                ];
            }


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
