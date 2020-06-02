<?php

namespace App\Http\Controllers;

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

//                $r->organisation()->associate($check->id);
//                $r->save();
//                $r->organisation()->attach($check->id);

//                $check->opportunities()->detach($r->id);
//                $check->opportunities()->attach($r->id);


                return $this->apiResponse->sendResponse(200, 'Opportunity Successfully Inserted', $data);

            } else {
                return $this->apiResponse->sendResponse(500, 'User unauthenticated', null);
            }

        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getTraceAsString());
        }
    }
}
