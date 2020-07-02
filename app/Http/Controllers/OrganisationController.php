<?php

namespace App\Http\Controllers;

use App\ActionUser;
use App\Analytics;
use App\EligibleRegion;
use App\FundType;
use App\Opportunity;
use App\OpportunityLocation;
use App\Organisation;
use App\OrganisationDetail;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Validator;

class OrganisationController extends Controller
{
    private $apiResponse;
    //
    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function post_opportunity(Request $request) {
        try {
            $check = Validator::make($request->all(), [
                'deadline' => 'required|date',
                'image' => 'required|mimes:jpeg,jpg,png',
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
                return $this->apiResponse->sendResponse(400, 'Bad Request', [$check->errors(), $request]);
            }

            $check = Organisation::where('id',$request->org_id)->first();
            if($check){
                $slug = str_replace(" ", "-", strtolower($request->title)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 16);

                $file = $request->file('image');

                $ext = "." . pathinfo($_FILES["image"]["name"])['extension'];


                $name = time() . uniqid() . $ext;

                $filePath = "opportunity_images/" . $name;

                Storage::putFileAs(
                    'public/', $file, $filePath
                );

                $opportunity = array(
//                Deadline = ongoing. Hack: Set it as unlikely date and have a flag for ongoing and handle in UI. Else: Animesh
                    'deadline' => $request->deadline,
                    'image' => $filePath,
//                Google home page in case of no link
                    'link' => $request->link,
                    'fund_type_id' => $request->fund_type,
                    'slug' => $slug,
                    'opportunity_location_id' => $request->opportunity_location,
                );

                $opportunity['en'] = [
                    'title' =>$request->title,
                    'description' => $request->description
                ];

                $r = Opportunity::create($opportunity);
                $r->tags()->sync($request->tags);
                $r->eligible_regions()->sync($request->eligible_regions);

                $data = [
                    "id" => $r->id,
                ];
                DB::table('opportunity_organisation')->insert(['opportunity_id' => $r->id, 'organisation_id' => $check->id]);

                return $this->apiResponse->sendResponse(200, 'Opportunity Successfully Inserted', $data);

            } else {
                return $this->apiResponse->sendResponse(500, 'User unauthenticated', null);
            }

        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    /*
Total Views [int]
Total time spent [int][in seconds]
    SUM(rows with key = duration & Organization opp)
Total Shares[int]
    SUM(rows with action_id = Share & Organization opp)
Total official link clicks [int]
    SUM(rows with action_id = official link & Organization opp)
Relevant not relevant [int : relevant]
    SUM(rows with action_id = Share & Organization opp)

Opportunity views on between two dates [array of ints]
    OpportunityView::where('create_at', >
Past log [15, paginated, events with timestamp, name of user]
    Analytics::where(opp in org)->paginate(15)
User country views [percentage, top 5]
Official link clicks per day
Read more clicks per day

Forum [list of questions]

Trending opportunities
     */

    public function analytics(Request $request){
        $total_views = DB::table('opportunity_views')->where('opportunity_id', $request->opp_id)->sum('views');
        $total_shares = DB::table('analytics')->where('opportunity_id', $request->opp_id)->where('action_id', '=', 2)->count();
        $total_time_spent = DB::table('analytics')->where('opportunity_id', $request->opp_id)->where('key', "duration")->sum('value');
        $total_official_link = DB::table('analytics')->where('opportunity_id', $request->opp_id)->where('action_id', 3)->count();
        $relevant = DB::table('analytics')->where('opportunity_id', $request->opp_id)->where('action_id', 6)->select('value', DB::raw('count(*) as total'))->groupBy('value')->get();
        $total_views_bw_dates = DB::table('opportunity_views')->where('opportunity_id', $request->opp_id)->select('created_at', DB::raw('SUM(views) as total'))->groupBy('created_at')->get();
        $last_few = Analytics::with('action')->where('opportunity_id', $request->opp_id)->paginate(15);

        $data = ["total_time_spent"=> $total_time_spent, "total_share"=>$total_shares, "total_official_link"=>$total_official_link];
        $data["relevant"] = $relevant;
        $data["total_views"] = $total_views;
        $data["total_views_bw_dates"] = $total_views_bw_dates;
        $data["last_few"] = $last_few;

        return $this->apiResponse->sendResponse(200, 'Analytics generated', $data);


        $opportunity = Opportunity::with(['actions', 'views', 'organisation' => function ($query) use ($request) {
            $query->where('id', $request->organisation_id);
        }])->get();

        return $this->apiResponse->sendResponse(200, 'Opportunity Successfully Inserted', $opportunity);
    }
}
