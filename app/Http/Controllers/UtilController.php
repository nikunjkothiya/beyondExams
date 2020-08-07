<?php

namespace App\Http\Controllers;

use App;
use App\EligibleRegion;
use App\FundType;
use App\Language;
use App\Opportunity;
use App\OpportunityLocation;
use App\PlusTransaction;
use App\Tag;
use App\User;
use App\VersionCode;
use Auth;
use Carbon\Carbon;
use DateTime;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use stdClass;
use Illuminate\Support\Facades\Validator;

use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class UtilController extends Controller
{
    protected $code = array();

    public function __construct()
    {
        $languages = Language::all();
        foreach ($languages as $lang) {
            $this->code[] = $lang->code;
        }
        $this->KEY = env('SYS_API_KEY');
    }

    public function locale($locale)
    {
        if (in_array($locale, $this->code)) {
            App::setLocale($locale);
            session()->put('locale', $locale);
            return redirect()->back();
        } else {
            App::setLocale('en');
            session()->put('locale', 'en');
            return redirect()->back();
        }
    }

    public function save_opportunity(Request $request)
    {
        try {
            $validator = $request->validate([
                'id' => 'required|exists:opportunities',
            ]);
            $id = $request->id;
            $user = Auth::user();
            $user->saved_opportunities()->detach($id);
            $user->saved_opportunities()->attach($id);
        } catch (Exception $e) {
        }
        return response()->json([
            'status' => 'success',
            'status_code' => '200',
            'message' => 'Opportunity Saved',
            'data' => ''
        ]);
    }

    public function unsave_opportunity(Request $request)
    {
        try {
            $validator = $request->validate([
                'id' => 'required|exists:opportunities',
            ]);
            $id = $request->id;
            $user = Auth::user();
            $user->saved_opportunities()->detach($id);
        } catch (Exception $e) {
        }
        return response()->json([
            'status' => 'success',
            'status_code' => '200',
            'message' => 'Removed Opportunity',
            'data' => ''
        ]);
    }

    public function request_guidance(Request $request)
    {
        try {
            $validator = $request->validate([
                'id' => 'required|exists:opportunities',
            ]);

            $user = Auth::user();

            $id = $request->id;

            $opp = Opportunity::find($id);

            $this->send($user->id, $user->name, $user->email, $id, $opp->title, $opp->deadline);

            $record = new PlusTransaction();
            $record->user_id = $user->id;
            $record->opportunity_id = $id;
            $record->status = 1;
            //            opp_title, off_link, opp_deadline
            //            user_name, email,
            $record->datetime = new DateTime();

            $record->save();
        } catch (Exception $e) {
            Log::error("FUCK IT!");
            Log::error($e);
            dd($e);
        }
        return response()->json([
            'status' => 'success',
            'status_code' => '200',
            'message' => 'Guidance requested',
            'data' => ''
        ]);
    }

    public function send($user_id, $user_name, $user_email, $opportunity_id, $opp_title, $opp_deadline)
    {
        $objMail = new stdClass();
        $objMail->user_id = $user_id;
        $objMail->user_name = $user_name;
        $objMail->user_email = $user_email;
        $objMail->opp_id = $opportunity_id;
        $objMail->opp_title = $opp_title;
        $objMail->opp_deadline = $opp_deadline;

        Mail::to("pankajbaranwal.1996@gmail.com")->send(new App\Mail\GuidanceMail($objMail));
    }

    public function next_opps()
    {
        $user = User::find(Auth::user()->id);
        $filter = array();
        $uts = $user->tags;
        foreach ($uts as $ut) {
            $filter[] = $ut->id;
        }
        $opportunities = Opportunity::with('tags')
            ->leftJoin('plus_transactions', function ($join) use ($user) {
                $join->on('plus_transactions.opportunity_id', '=', 'opportunities.id');
                $join->where('plus_transactions.user_id', '=', $user->id);
            })
            ->whereHas('tags', function ($q) use ($filter) {
                $q->whereIn('opportunities.id', $filter);
            })->whereDate('opportunities.deadline', '>=', Carbon::today()->toDateString())->orderBy('opportunities.deadline', 'ASC')->select('opportunities.*', 'plus_transactions.status')->paginate(1);

        return response()->json($opportunities);
    }

    public function prev_opps()
    {
        $user = User::find(Auth::user()->id);
        $filter = array();
        $uts = $user->tags;
        foreach ($uts as $ut) {
            $filter[] = $ut->id;
        }
        $opportunities = Opportunity::with('tags')
            ->leftJoin('plus_transactions', function ($join) use ($user) {
                $join->on('plus_transactions.opportunity_id', '=', 'opportunities.id');
                $join->where('plus_transactions.user_id', '=', $user->id);
            })
            ->whereHas('tags', function ($q) use ($filter) {
                $q->whereIn('id', $filter);
            })->whereDate('deadline', '>=', Carbon::today()->toDateString())->orderBy('deadline', 'ASC')->select('opportunities.*', 'plus_transactions.status')->paginate(1);
        return response()->json($opportunities);
    }

    public function next_saved_opps()
    {
        $user = User::find(Auth::user()->id);
        $oppids = array();
        $opps = $user->saved_opportunities;
        foreach ($opps as $opp) {
            $oppids[] = $opp->id;
        }
        $opportunities = Opportunity::with('tags')->whereIn('id', $oppids)->orderBy('deadline', 'ASC')->paginate(1);
        return response()->json($opportunities);
    }

    public function post_opportunity(Request $request)
    {
        $apiResponse = new ApiResponse;
        $data = json_decode($request->all()["data"], true);
        try {
            $check = Validator::make($data, [
                'token' => 'required|string',
                'deadline' => 'required|date',
                'image' => 'required|string',
                'link' => 'required|string',
                //               NA fundtype = 3, PF = 2, FF = 1
                'fund_type' => 'required|integer|min:1|max:' . FundType::count(),
                //                Many2many: To handle multiple origins
                //                Replace null with online
                'opportunity_location' => 'required|integer|min:1|max:' . OpportunityLocation::count(),

                'tags' => 'required|array|min:1',
                'tags.*' => 'integer|min:1|max:' . Tag::count(),
                'eligible_regions' => 'required|array|min:1',
                'eligible_regions.*' => 'integer|min:1|max:' . EligibleRegion::count(),
            ]);
            if ($check->fails()) {
                return $apiResponse->sendResponse(400, 'Bad Request', [$check->errors(), $request]);
            }

            $data = json_decode($request->all()["data"]);
            if ($data->token != $this->KEY) {
                return $apiResponse->sendResponse(401, 'Unauthorized Request', '');
            }

            if (isset($data->legacy_id)) {
                if (DB::table('legacy_opportunities')->where('legacy_opportunity_id', $data->legacy_id)->exists()) {
                    return $apiResponse->sendResponse(500, 'Opportunity already exists', $data->legacy_id);
                }
            }

            $slug = str_replace(" ", "-", strtolower($data->en->title)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 16);
            $opportunity = array(
                // Deadline = ongoing. Hack: Set it as unlikely date and have a flag for ongoing and handle in UI. Else: Animesh
                'deadline' => $data->deadline,
                'image' => $data->image,
                // Google home page in case of no link
                'link' => $data->link,
                'fund_type_id' => $data->fund_type,
                'slug' => $slug,
                'opportunity_location_id' => $data->opportunity_location,
            );

            if (isset($data->bn)) {
                $opportunity['bn'] = [
                    'title' => $data->bn->title,
                    'description' => $data->bn->description
                ];
            }
            if (isset($data->my)) {
                $opportunity['my'] = [
                    'title' => $data->my->title,
                    'description' => $data->my->description
                ];
            }
            if (isset($data->en)) {
                $opportunity['en'] = [
                    'title' => $data->en->title,
                    'description' => $data->en->description
                ];
            }
            if (isset($data->tl)) {
                $opportunity['tl'] = [
                    'title' => $data->tl->title,
                    'description' => $data->tl->description
                ];
            }
            if (isset($data->fr)) {
                $opportunity['fr'] = [
                    'title' => $data->fr->title,
                    'description' => $data->fr->description
                ];
            }
            if (isset($data->de)) {
                $opportunity['de'] = [
                    'title' => $data->de->title,
                    'description' => $data->de->description
                ];
            }
            if (isset($data->hi)) {
                $opportunity['hi'] = [
                    'title' => $data->hi->title,
                    'description' => $data->hi->description
                ];
            }
            // if (isset($data->id)){
            // $opportunity['id'] = [
            // 'title' =>$data->id->title,
            // 'description' => $data->id->description
            // ];
            // }
            if (isset($data->it)) {
                $opportunity['it'] = [
                    'title' => $data->it->title,
                    'description' => $data->it->description
                ];
            }
            if (isset($data->ja)) {
                $opportunity['ja'] = [
                    'title' => $data->ja->title,
                    'description' => $data->ja->description
                ];
            }
            if (isset($data->km)) {
                $opportunity['km'] = [
                    'title' => $data->km->title,
                    'description' => $data->km->description
                ];
            }
            if (isset($data->ko)) {
                $opportunity['ko'] = [
                    'title' => $data->ko->title,
                    'description' => $data->ko->description
                ];
            }
            if (isset($data->lo)) {
                $opportunity['lo'] = [
                    'title' => $data->lo->title,
                    'description' => $data->lo->description
                ];
            }
            if (isset($data->ms)) {
                $opportunity['ms'] = [
                    'title' => $data->ms->title,
                    'description' => $data->ms->description
                ];
            }
            if (isset($data->ne)) {
                $opportunity['ne'] = [
                    'title' => $data->ne->title,
                    'description' => $data->ne->description
                ];
            }
            if (isset($data->ro)) {
                $opportunity['ro'] = [
                    'title' => $data->ro->title,
                    'description' => $data->ro->description
                ];
            }
            if (isset($data->ru)) {
                $opportunity['ru'] = [
                    'title' => $data->ru->title,
                    'description' => $data->ru->description
                ];
            }
            if (isset($data->si)) {
                $opportunity['si'] = [
                    'title' => $data->si->title,
                    'description' => $data->si->description
                ];
            }
            if (isset($data->es)) {
                $opportunity['es'] = [
                    'title' => $data->es->title,
                    'description' => $data->es->description
                ];
            }
            if (isset($data->ta)) {
                $opportunity['ta'] = [
                    'title' => $data->ta->title,
                    'description' => $data->ta->description
                ];
            }
            if (isset($data->th)) {
                $opportunity['th'] = [
                    'title' => $data->th->title,
                    'description' => $data->th->description
                ];
            }
            if (isset($data->vi)) {
                $opportunity['vi'] = [
                    'title' => $data->vi->title,
                    'description' => $data->vi->description
                ];
            }


            $r = Opportunity::create($opportunity);
            $r->tags()->sync($data->tags);
            $r->eligible_regions()->sync($data->eligible_regions);
            if (isset($data->legacy_id)) {
                DB::table('legacy_opportunities')->insertOrIgnore(array('phoenix_opportunity_id' => $r->id, 'legacy_opportunity_id' => $data->legacy_id));
            }

            $data = [
                "id" => $r->id,
                "legacy_id" => $data->legacy_id
            ];

            return $apiResponse->sendResponse(200, 'Opportunity Successfully Inserted', $data);
        } catch (\Exception $e) {
            return $apiResponse->sendResponse(500, $e->getMessage(), $e);
        }
    }

    public function submit_guidance_request(Request $request)
    {
        $apiResponse = new ApiResponse;
        try {
            $validator = $request->validate([
                'id' => 'required|int',
            ]);

            // if ($validator->fails()) {
            // return $apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            // }

            // $user = User::find($request->user_id);

            $user = Auth::user();
            $legacy_opportunity_id = DB::table('legacy_opportunities')->where('phoenix_opportunity_id', $request->id)->select('legacy_opportunity_id')->get();
            if (count($legacy_opportunity_id) == 0) {
                return $apiResponse->sendResponse(400, 'Opportunity not found in legacy', null);
            }
            $legacy_opportunity_id = $legacy_opportunity_id[0]->legacy_opportunity_id;
            $legacy_user_id = DB::table('legacy_users')->where('phoenix_user_id', $user->id)->select('legacy_user_id')->get();

            if (count($legacy_user_id) == 0) {
                return $apiResponse->sendResponse(400, 'User not found in legacy', null);
            }

            $legacy_user_id = $legacy_user_id[0]->legacy_user_id;


            $client = new Client();

            $res = $client->request('POST', 'https://lithics.in/apis/mauka/precisely_chat/create_chat.php', [
                'form_params' => [
                    'user_id' => $legacy_user_id,
                    'opp_id' => $legacy_opportunity_id
                ]
            ]);

            $result = $res->getBody()->getContents();
            return $apiResponse->sendResponse(200, 'Guidance request placed', json_decode($result, true));
        } catch (\Exception $e) {
            return $apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function generate_all_sitemap()
    {
        $apiResponse = new ApiResponse;
        try {
            // Get sitemap index
            $lastOpp = Opportunity::latest('id')->first();
            $index = floor($lastOpp->id / 1000);
            
            resolve('url')->forceRootUrl('https://app.precisely.co.in/opportunity');
            $sitemapIndex = SitemapIndex::create();
            for($i = 0;$i <= $index; $i++){
                // Get Last 1000 Opportunity
                $opportunities = Opportunity::where('id','>', ($i * 1000))->limit(1000)->get();
                // Start making sitemap
                $sitemap =  Sitemap::create();
                // Loop through all opp 
                foreach($opportunities as $opportunity){
                    $sitemap->add(Url::create($opportunity->slug)->setPriority(0.5));
                }
                // Write to disk
                $sitemap_path = 'sitemaps/sitemap_' . ($i + 1) . '.xml';
                $sitemap->writeToDisk('public', $sitemap_path);
                $sitemapIndex->add($sitemap_path);
            }
            $sitemapIndex->writeToDisk('public', 'sitemaps/sitemap_index.xml');
            resolve('url')->forceRootUrl(env('APP_URL'));
            return  $apiResponse->sendResponse(200, "Success", $index);
        } catch (\Exception $e) {
            return  $apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function generate_latest_sitemap()
    {
        $apiResponse = new ApiResponse;
        try {
            // Get sitemap index
            $lastOpp = Opportunity::latest('id')->first();
            $index = floor($lastOpp->id / 1000);
            // Get Last 1000 Opportunity
            $opportunities = Opportunity::where('id','>', ($index * 1000))->limit(1000)->get();
            // Start making sitemap
            resolve('url')->forceRootUrl('https://app.precisely.co.in/opportunity');
            $sitemap =  Sitemap::create();
            // Loop through all opp 
            foreach($opportunities as $opportunity){
                $sitemap->add(Url::create($opportunity->slug)->setPriority(0.5));
            }
            // Write to disk
            $path = 'sitemaps/sitemap_' . ($index + 1) . '.xml';
            $sitemap->writeToDisk('public', $path);

            // Generate Sitemap Index
            $sitemapIndex = SitemapIndex::create();
            for($i = 0;$i <= $index; $i++){
                $sitemap_path = 'sitemaps/sitemap_' . ($i + 1) . '.xml';
                $sitemapIndex->add($sitemap_path);
            }
            $sitemapIndex->writeToDisk('public', 'sitemaps/sitemap_index.xml');

            resolve('url')->forceRootUrl(env('APP_URL'));
            return  $apiResponse->sendResponse(200, "Success", $index);
        } catch (\Exception $e) {
            return  $apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_version_code(Request $request)
    {
        $apiResponse = new ApiResponse;
        try {
            $validator = Validator::make($request->all(), [
                "version_code"  => "required",
                "version_name" => "required"
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $vc = new VersionCode();
            $vc->version_code = $request->version_code;
            $vc->version_name = $request->version_name;
            $vc->save();

            return $apiResponse->sendResponse(200, 'Version Added', $vc);
        } catch (\Exception $e) {
            return $apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}
