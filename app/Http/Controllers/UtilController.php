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
use Auth;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use stdClass;
use Validator;

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
        try {

            $apiResponse = new ApiResponse;
            $check = Validator::make($request->all(), [
                'token' => 'required|string',
                'deadline' => 'required|date',
                'image' => 'required|string',
                'link' => 'required|string',
                'fund_type' => 'required|integer|min:1|max:' . FundType::count(),
                'opportunity_location' => 'required|integer|min:1|max:' . OpportunityLocation::count(),

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
                'tags.*' => 'integer|min:1|max:' . Tag::count(),
                'eligible_regions' => 'required|array|min:1',
                'eligible_regions.*' => 'integer|min:1|max:' . EligibleRegion::count(),
            ]);
            if ($check->fails()) {
                return $apiResponse->sendResponse(400, 'Bad Request', $check->errors());
            }
            if ($request->token != $this->KEY) {
                return $apiResponse->sendResponse(401, 'Unauthorized Request', '');
            }
            $slug = str_replace(" ", "-", strtolower($request->en['title'])) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 16);
            $opportunity = array(
                'deadline' => $request->deadline,
                'image' => $request->image,
                'link' => $request->link,
                'fund_type_id' => $request->fund_type,
                'slug' => $slug,
                'opportunity_location_id' => $request->opportunity_location,
            );

            $r = Opportunity::create($opportunity);
            $r->tags()->sync($request->tags);
            $r->eligible_regions()->sync($request->eligible_regions);

            $data = [
                "id" => $r->id,
            ];

            return $apiResponse->sendResponse(200, 'Opportunity Successfully Inserted', $data);
        } catch (Exception $e) {
            return $apiResponse->sendResponse(500, 'Internal Server Error', '');
        }
    }
}
