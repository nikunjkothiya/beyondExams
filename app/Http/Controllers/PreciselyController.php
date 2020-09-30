<?php

namespace App\Http\Controllers;

use App\Action;
use App\ActionProperty;
use App\ActionUser;
use App\ActionUserOpportunity;
use App\Analytics;
use App\Country;
use App\Currency;
use App\Discipline;
use App\Domain;
use App\DomainUser;
use App\FileType;
use App\Language;
use App\Opportunity;
use App\Qualification;
use App\Resource;
use App\ResourceKey;
use App\Tag;
use App\Transaction;
use App\User;
use App\UserDetail;
use App\MentorDetail;
use App\MentorVerification;
use Carbon\Carbon;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ApiResponse;
use App\Organisation;
use App\OrganisationDetail;
use App\StudentDetail;
use Illuminate\Support\Facades\Storage;
use Auth;

/**
 * @property ApiResponse apiResponse
 */
class PreciselyController extends Controller
{
    protected $txnflag;
    private $base_url = 'https://precisely-test1221001-dev.s3.ap-south-1.amazonaws.com/';

    public function __construct(ApiResponse $apiResponse)
    {
        $this->msg = "";
        $this->apiResponse = $apiResponse;
        $this->txnflag = new SubscriptionController($apiResponse);
    }

    public function get_language(Request $request)
    {
        try {
            $languages = DB::table('languages')->select('id', 'code', 'language', 'language_example')->get();

            return $this->apiResponse->sendResponse(200, 'All languages fetched successfully', $languages);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }


    public function get_filters(Request $request)
    {
        try {
            $filters = Tag::all();

            return $this->apiResponse->sendResponse(200, 'All Tags fetched successfully', $filters);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }

    public function submit_mentor_profile(Request $request)
    {
        try {
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                $user_id = $user->id;
                $validator = Validator::make($request->all(), [
                    'firstname' => 'required|string|max:255',
                    'lastname' => 'string|max:255',
                    'email' => 'required|email',
                    'designation' => 'string|max:255',
                    'organisation' => 'string|max:255',
                    'profile_link' => 'string|max:1024',
                    'avatar' => 'image',
                    'price' => 'between:0,999.99',
                    'currency_id' => 'int|min:0|max:' . Currency::count(),
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }

                // Save avatar if its given
                $img = null;
                if (isset($request->avatar)) {
                    $aws_root = "public/";
                    $file = $request->file('avatar');
                    $ext = "." . pathinfo($_FILES["avatar"]["name"])['extension'];
                    $name = time() . uniqid() . $ext;
                    $contents = file_get_contents($file);
                    $filePath = "avatars/" . $name;
                    Storage::disk('s3')->put($aws_root . $filePath, $contents);
                    $user->avatar = $this->base_url . $aws_root . $filePath;
                    $user->save();
                    $img = $this->base_url . $aws_root . $filePath;
                }

                // Save data to users table
                if (isset($request->lastname)){
                    $user->name = $request->firstname . ' ' . $request->lastname;
                } else {
                    $user->name = $request->firstname;
                }
                $user->email = $request->email;
                $user->save;


                // Set commono data to user_details table
                $details = UserDetail::where('user_id', $user_id)->first();
                if (is_null($details)){
                    $details = new UserDetail();
                    $details->user_id = $user_id;
                }
                /* $details->user_id = $user_id;*/
                if (isset($request->firstname))
                    $details->firstname = $request->firstname;
                if (isset($request->lastname))
                    $details->lastname = $request->lastname;
                if (isset($request->email))
                    $details->email = $request->email;
                if (isset($request->phone))
                    $details->phone = $request->phone;
                if (isset($request->profile_link))
                    $details->profile_link = $request->profile_link;
                if (isset($request->profile_link))
                    $details->profile_link = $request->profile_link;
                if (isset($request->avatar) && !is_null($img))
                    $details->avatar = $img;

                $slug = str_replace(" ", "-", strtolower($request->firstname . $request->lastname)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 16);
                $details->slug = $slug;
                $details->save();

                // Updating Specific mentor details
                $check = MentorDetail::where('user_id', $user_id)->first();
                if (is_null($check)) {
                    $record = new MentorDetail();
                    $record->user_id = $user_id;
                    if (isset($request->price) && isset($request->currency_id)){
                        $record->price = $request->price;
                        $record->currency_id = $request->currency_id;
                    }
                    if (isset($request->designation))
                        $record->designation = $request->designation;
                    if (isset($request->organisation))
                        $record->organisation = $request->organisation;
                    $record->save();
                    // Send Flags
                    if ($record) {
                        $flag = 2;
                        $verified = MentorVerification::where('user_id', $user_id)->first();
                        if (isset($request->refer_code)) {
                            if ($request->refer_code == 'PRECISELY-TUTOR-PASS') {
                                $verified->is_verified = 1;
                                $verified->save();
                            }
                        }
                        if ($verified->is_verified == 0) {
                            // Mentor Details filled but not verified
                            $flag = 2;
                        } elseif ($verified->is_verified == 1) {
                            // Mentor Verified
                            $flag = 1;
                        }
                        $record->flag = $flag;
                        $record->save();
                        $record['new'] = $flag;
                        return $this->apiResponse->sendResponse(200, 'Mentor details saved.', array_merge($details->toArray(), $record->toArray()));
                    } else {
                        return $this->apiResponse->sendResponse(500, 'Internal server error. New record could not be inserted', null);
                    }
                } else {
                    if (isset($request->designation))
                        $check->designation = $request->designation;
                    if (isset($request->organisation))
                        $check->organisation = $request->organisation;
                    $check->save();

                    if ($check) {
                        $flag = 2;
                        $verified = MentorVerification::where('user_id', $user_id)->first();
                        if (isset($request->refer_code)) {
                            if ($request->refer_code == 'PRECISELY-TUTOR-PASS') {
                                $verified->is_verified = 1;
                                $verified->save();
                            }
                        }
                        if ($verified->is_verified == 0) {
                            // Mentor Details filled but not verified
                            $flag = 2;
                        } elseif ($verified->is_verified == 1) {
                            // Mentor Verified
                            $flag = 1;
                        } elseif ($verified->is_verified == 2) {
                            // Mentor Rejected
                            $flag = 3;
                        }
                        $check->flag = $flag;
                        $check->save();
                        $check['new'] = $flag;
                        return $this->apiResponse->sendResponse(200, 'Mentor details saved.', array_merge($details->toArray(), $check->toArray()));
                    } else {
                        return $this->apiResponse->sendResponse(500, 'Internal server error. Record could not be updated', null);
                    }
                }
            }
            return $this->apiResponse->sendResponse(401, "User not found", null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function submit_user_profile(Request $request)
    {
        try {
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                $user_id = $user->id;
                $validator = Validator::make($request->all(), [
                    'firstname' => 'required|string|max:255',
                    'lastname' => 'required|string|max:255',
                    'college' => 'required|string|max:1024',
                    'gpa' => 'numeric|between:0,10.00',
                    'qualification' => 'required|integer|min:1|max:' . Qualification::count(),
                    'discipline' => 'required|integer|min:1|max:' . Discipline::count(),
                    'city' => 'string|max:255',
                    'country' => 'required|integer|min:1|max:' . Country::count(),
                    'email' => 'required|email',
                    'phone' => 'string',
                    'profile_link' => 'string',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }

                // Save data to users table
                $user->name = $request->firstname . ' ' . $request->lastname;
                $user->email = $request->email;
                $user->save;

                // Updating Common User Details
                $details = UserDetail::where('user_id', $user_id)->first();
                $details->user_id = $user_id;
                if (isset($request->firstname))
                    $details->firstname = $request->firstname;
                if (isset($request->lastname))
                    $details->lastname = $request->lastname;
                if (isset($request->lastname))
                    $details->email = $request->email;
                if (isset($request->phone))
                    $details->phone = $request->phone;
                if (isset($request->profile_link))
                    $details->profile_link = $request->profile_link;
                if (!is_null($user->avatar))
                    $details->avatar = $user->avatar;

                $details->language_id = Language::where('code', Config::get('app.locale'))->first()->id;
                $slug = str_replace(" ", "-", strtolower($request->firstname . $request->lastname)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 16);
                $details->slug = $slug;
                $details->save();

                // Updating Student Specific details
                $check = StudentDetail::where('user_id', $user_id)->first();
                if (is_null($check)) {
                    $record = new StudentDetail();
                    $record->user_id = $user_id;
                    if (isset($request->college))
                        $record->college = $request->college;
                    if (isset($request->city))
                        $record->city = $request->city;
                    if (isset($request->gpa))
                        $record->gpa = $request->gpa;
                    if (isset($request->qualification_id))
                        $record->qualification_id = $request->qualification;
                    if (isset($request->discipline_id))
                        $record->discipline_id = $request->discipline;
                    if (isset($request->country_id))
                        $record->country_id = $request->country;

                    $record->save();
                    if ($record) {
                        // Check if tags are filled if filled then 0 else 3
                        $check_tag = DB::table('tag_user')->select('tag_id')->where('user_id', $user_id)->first();
                        if ($check_tag) {
                            $flag = 0;
                        } else {
                            $flag = 3;
                        }
                        $responseArray = [
                            'new' => $flag
                        ];
                        $record->flag = $flag;
                        $record->save();
                        if (isset($request->domain_ids)) {
                            foreach ($request->domain_ids as $domain) {
                                $domain_user = new DomainUser();
                                $domain_user->user_id = Auth::user()->id;
                                $domain_user->domain_id = $domain;
                                $domain_user->save();
                            }
                        }
                        return $this->apiResponse->sendResponse(200, 'User details saved', $responseArray);
                    } else {
                        return $this->apiResponse->sendResponse(500, 'Internal server error. New record could not be inserted', null);
                    }
                } else {
                    $check->user_id = $user_id;
                    if (isset($request->college))
                        $check->college = $request->college;
                    if (isset($request->city))
                        $check->city = $request->city;
                    if (isset($request->gpa))
                        $check->gpa = $request->gpa;
                    if (isset($request->qualification_id))
                        $check->qualification_id = $request->qualification;
                    if (isset($request->discipline_id))
                        $check->discipline_id = $request->discipline;
                    if (isset($request->country_id))
                        $check->country_id = $request->country;

                    $check->save();
                    if ($check) {
                        // Check if tags are filled if filled then 0 else 3
                        $check_tag = DB::table('tag_user')->select('tag_id')->where('user_id', $user_id)->first();
                        if ($check_tag) {
                            $flag = 0;
                        } else {
                            $flag = 3;
                        }
                        $responseArray = [
                            'new' => $flag
                        ];
                        $check->flag = $flag;
                        $check->save();
                        if (isset($request->domain_ids)) {
                            foreach ($request->domain_ids as $domain) {
                                $domain_user = new DomainUser();
                                $domain_user->user_id = Auth::user()->id;
                                $domain_user->domain_id = $domain;
                                $domain_user->save();
                            }
                        }
                        return $this->apiResponse->sendResponse(200, 'User details saved.', $responseArray);
                    } else {
                        return $this->apiResponse->sendResponse(500, 'Internal server error. Record could not be updated', null);
                    }
                }
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function submit_org_profile(Request $request)
    {
        try {
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                $user_id = $user->id;
                $validator = Validator::make($request->all(), [
                    'firstname' => 'required|string|max:255',
                    'lastname' => 'required|string|max:255',
                    'contact_person' => 'required|string|max:1024',
                    'branch' => 'required|numeric|between:0,10.00',
                    'country_id' => 'required|integer|min:1|max:' . Country::count(),
                    'email' => 'required|email',
                    'phone' => 'string',
                    'profile_link' => 'string',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }

                // Updating Common User Details
                $details = UserDetail::where('user_id', $user_id)->first();
                $details->user_id = $user_id;
                if (isset($request->firstname))
                    $details->firstname = $request->firstname;
                if (isset($request->lastname))
                    $details->lastname = $request->lastname;
                if (isset($request->lastname))
                    $details->email = $request->email;
                if (isset($request->phone))
                    $details->phone = $request->phone;
                if (isset($request->profile_link))
                    $details->profile_link = $request->profile_link;

                $details->language_id = Language::where('code', Config::get('app.locale'))->first()->id;
                $slug = str_replace(" ", "-", strtolower($request->firstname . $request->lastname)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 16);
                $details->slug = $slug;
                $details->save();

                // Updating Student Specific details
                $check = OrganisationDetail::where('user_id', $user_id)->first();
                if (is_null($check)) {
                    $record = new OrganisationDetail();
                    $record->user_id = $user_id;
                    if (isset($request->contact_person))
                        $record->contact_person = $request->contact_person;
                    if (isset($request->branch))
                        $record->branch = $request->branch;
                    if (isset($request->country_id))
                        $record->country_id = $request->country_id;
                    $record->save();
                    if ($record) {
                        return $this->apiResponse->sendResponse(200, 'Org details saved', array_merge($record->toArray(), $details->toArray()));
                    } else {
                        return $this->apiResponse->sendResponse(500, 'Internal server error. New record could not be inserted', null);
                    }
                } else {
                    $check->user_id = $user_id;
                    if (isset($request->contact_person))
                        $check->contact_person = $request->contact_person;
                    if (isset($request->branch))
                        $check->branch = $request->branch;
                    if (isset($request->country_id))
                        $check->country_id = $request->country_id;
                    $check->save();
                    if ($check) {
                        return $this->apiResponse->sendResponse(200, 'Org details saved.', array_merge($check->toArray(), $details->toArray()));
                    } else {
                        return $this->apiResponse->sendResponse(500, 'Internal server error. Record could not be updated', null);
                    }
                }
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    //    TODO: CORRECT RETURN TYPE
    public function get_user_profile()
    {
        if (Auth::check()) {
            $user = User::find(Auth::user()->id);
            try {
                $pcheck = UserDetail::where('user_id', $user->id)->first();
            } catch (Exception $e) {
                return $this->apiResponse->sendResponse(500, 'User authentication failed', $e->getMessage());
            }

            if ($pcheck) {
                $dcheck = StudentDetail::where('user_id', $user->id)->first();
                if ($dcheck) {
                    $data['user_details'] = array_merge($pcheck->toArray(), $dcheck->toArray());
                } else {
                    $data['user_details'] = $pcheck;
                }
                $avatar = DB::table('users')->select('avatar')->where('id', $user->id)->get();
                foreach ($avatar as $ava) {
                    $data['avatar'] = $ava->avatar;
                    break;
                }

                return $this->apiResponse->sendResponse(200, 'Successfully fetched user profile.', $data);
            }
        } else {
            return $this->apiResponse->sendResponse(500, 'Users not logged in', null);
        }
    }

    public function get_mentor_profile()
    {
        $user = Auth::user();
        try {
            $pcheck = UserDetail::where('user_id', $user->id)->first();
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'User authentication failed', $e->getMessage());
        }

        if ($pcheck) {
            $dcheck = MentorDetail::where('user_id', $user->id)->first();
            if ($dcheck) {
                $data['mentor_details'] = array_merge($pcheck->toArray(), $dcheck->toArray());
            } else {
                $data['mentor_details'] = $pcheck;
            }
            $avatar = DB::table('users')->select('avatar')->where('id', $user->id)->get();
            foreach ($avatar as $ava) {
                $data['avatar'] = $ava->avatar;
                break;
            }

            // Update Flags
            if ($dcheck) {
                $verified = MentorVerification::where('user_id', $user->id)->first();

                if ($verified->is_verified == 0) {
                    // Mentor Details filled but not verified
                    $flag = 2;
                } else {
                    // Mentor Verified
                    $flag = 1;
                }
                $dcheck->flag = $flag;
                $dcheck->save();
            }


            return $this->apiResponse->sendResponse(200, 'Successfully fetched mentor profile.', $data);
        } else {
            return $this->apiResponse->sendResponse(404, 'Mentor profile needs to be filled', null);
        }
    }

    public function get_mentor_profile_from_slug(Request $request, $slug)
    {

        try {
            $pcheck = UserDetail::where('slug', $slug)->first();
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(400, 'Invalid slug', $e->getMessage());
        }

        if ($pcheck) {
            $dcheck = MentorDetail::where('user_id', $pcheck->user_id)->first();

            if ($dcheck) {
                $data['mentor_details'] = array_merge($pcheck->toArray(), $dcheck->toArray());
            } else {
                $data['mentor_details'] = $pcheck;
            }
	    $data['mentor_details']["currency"] = Currency::find($data["mentor_details"]["currency_id"]);

            $avatar = DB::table('users')->select('avatar')->where('id', $pcheck->user_id)->first();
            $data['avatar'] = $avatar->avatar;

            // Update Flags
            if ($dcheck) {
                $verified = MentorVerification::where('user_id', $pcheck->user_id)->first();

                if ($verified->is_verified == 0) {
                    // Mentor Details filled but not verified
                    $flag = 2;
                } else {
                    // Mentor Verified
                    $flag = 1;
                }
                $dcheck->flag = $flag;
                $dcheck->save();
            }


            return $this->apiResponse->sendResponse(200, 'Successfully fetched mentor profile.', $data);
        } else {
            return $this->apiResponse->sendResponse(404, 'Mentor profile needs to be filled', null);
        }
    }

    public function get_org_profile()
    {
        $user = Auth::user();
        try {
            $pcheck = UserDetail::where('user_id', $user->id)->first();
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'User authentication failed', $e->getMessage());
        }

        if ($pcheck) {
            $dcheck = OrganisationDetail::where('user_id', $user->id)->first();
            if ($dcheck) {
                $data['user_details'] = array_merge($pcheck->toArray(), $dcheck->toArray());
            } else {
                $data['user_details'] = $pcheck;
            }
            $avatar = DB::table('users')->select('avatar')->where('id', $user->id)->get();
            foreach ($avatar as $ava) {
                $data['avatar'] = $ava->avatar;
                break;
            }
            // $data['txnflag']=$this->txnflag->check_subscription($request->user_id);

            return $this->apiResponse->sendResponse(200, 'Successfully fetched org profile.', $data);
        } else {
            return $this->apiResponse->sendResponse(404, 'Org profile needs to be filled', null);
        }
    }

    public function save_opportunity(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:opportunities',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                try {
                    $opp_id = $request->id;
                    $user = User::where('id', $user->id)->first();
                    $check = DB::table('opportunity_user')->select('opportunity_id')->where('user_id', $user->id)->get();
                    if ($check == null) {
                        DB::table('opportunity_user')->insert(['opportunity_id' => $opp_id, 'user_id' => $user->id]);
                    } else {
                        $flag = 0;
                        foreach ($check as $c) {
                            if ($c->opportunity_id == $opp_id) {
                                $flag = 1;
                                break;
                            }
                        }
                        if ($flag == 0) {
                            DB::table('opportunity_user')->insert(['opportunity_id' => $opp_id, 'user_id' => $user->id]);
                        }
                    }
                } catch (Exception $e) {
                    return $this->apiResponse->sendResponse(500, 'User authentication failed', $e->getMessage());
                }
            } else {
                $this->apiResponse->sendResponse(400, 'Not Authorized', null);
            }

            // try{
            //   $id = $request->id;
            //   $user = UserDetail::where('user_id',$request->user_id)->first();
            //   $user->saved_opportunities()->detach($id);
            //   $user->saved_opportunities()->attach($id);
            // }
            // catch(Exception $e){
            //   return $this->apiResponse->sendResponse(500, 'User authentication failed', $e->getMessage());
            // }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
        return $this->apiResponse->sendResponse(200, 'Opportunity saved', null);
    }

    public function unsave_opportunity(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:opportunities',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                try {
                    $opp_id = $request->id;
                    $user = User::where('id', $user->id)->first();
                    $check = DB::table('opportunity_user')->select('opportunity_id')->where('user_id', $user->id)->get();
                    if ($check == null) {
                        return $this->apiResponse->sendResponse(200, 'Opportunity Unsaved', null);
                    } else {
                        foreach ($check as $c) {
                            if ($c->opportunity_id == $opp_id) {
                                DB::table('opportunity_user')->where([['user_id', $user->id], ['opportunity_id', $opp_id]])->delete();
                                return $this->apiResponse->sendResponse(200, 'Opportunity Unsaved', null);
                                break;
                            }
                        }
                    }
                } catch (Exception $e) {
                    return $this->apiResponse->sendResponse(500, 'User authentication failed', $e->getMessage());
                }
            } else {
                $this->apiResponse->sendResponse(400, 'Not Authorized', null);
            }

            // $id = $request->id;
            // $user = UserDetail::where('user_id',$request->user_id)->first();
            // $user->saved_opportunities()->detach($id);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
        return $this->apiResponse->sendResponse(200, 'Opportunity Unsaved', null);
    }

    public function show_saved_opportunity()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $saved_opportunities = $user->saved_opportunities()->with(['location', 'fund_type', 'opportunity_translations' => function ($query) {
                $query->where('locale', 'en');
            }, 'tags' => function ($query) {
                $query->select('id', 'tag');
            }])->paginate(10);

            return $this->apiResponse->sendResponse(200, 'Success', $saved_opportunities);
        } else {
            return $this->apiResponse->sendResponse(500, 'Unauthorized', null);
        }
    }

    public function save_user_language(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:languages',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }


        if (Auth::check()) {
            $user = User::find(Auth::user()->id);

            try {
                $pcheck = UserDetail::where('user_id', $user->id)->first();
            } catch (Exception $e) {
                return $this->apiResponse->sendResponse(500, 'User authentication failed', $e->getMessage());
            }

            if ($pcheck) {
                $pcheck->language_id = $request->id;
                $pcheck->save();
                // Flags
                $flag = 2;
                $check_detail = StudentDetail::where('user_id', $user->id)->first();
                $check_tag = DB::table('tag_user')->select('tag_id')->where('user_id', $user->id)->first();
                if ($check_detail) {
                    // Check User Details is filled
                    if ($check_tag) {
                        // If Category is filled
                        $flag = 0;
                    } else {
                        // If Category is not filled
                        $flag = 3;
                    }
                    $check_detail->flag = $flag;
                    $check_detail->save();
                } else {
                    // Check User Details is not filled
                    $flag = 2;
                }
                $responseArray = [
                    'new' => $flag
                ];
                return $this->apiResponse->sendResponse(200, 'Success', $responseArray);
            } else {
                $record = new UserDetail;
                $record->user_id = $user->id;
                $record->language_id = $request->id;
                $record->save();
                // Flags
                $flag = 2;
                $check_detail = StudentDetail::where('user_id', $user->id)->first();
                $check_tag = DB::table('tag_user')->select('tag_id')->where('user_id', $user->id)->first();
                if ($check_detail) {
                    if ($check_tag) {
                        // If Category is filled
                        $flag = 0;
                    } else {
                        // If Category is not filled
                        $flag = 3;
                    }
                    $check_detail->flag = $flag;
                    $check_detail->save();
                } else {
                    $flag = 2;
                }
                $responseArray = [
                    'new' => $flag
                ];
                return $this->apiResponse->sendResponse(200, 'Success', $responseArray);
            }
        } else {
            return $this->apiResponse->sendResponse(500, 'Users not logged in', null);
        }
    }

    public function save_user_filters(Request $request)
    {
        try {
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
            } else {
                return $this->apiResponse->sendResponse(500, 'Un Authorized', null);
            }

            $user = User::where('id', $user->id)->first();
            $tags = $request->tags;
            // return $this->apiResponse->sendResponse(200, 'Saved filters selected by user', $tags);
            if (empty($tags)) {
                return $this->apiResponse->sendResponse(400, 'Select at least one filter', null);
            }

            // Read $tags as json
            $user->tags()->sync(json_decode($tags));
            // Check if profile are filled if filled then 0 else 2
            $check_detail = StudentDetail::where('user_id', $user->id)->first();
            if ($check_detail) {
                $flag = 0;
                $check_detail->flag = $flag;
                $check_detail->save();
            } else {
                $flag = 2;
            }
            $responseArray = [
                'new' => $flag
            ];
            return $this->apiResponse->sendResponse(200, 'Saved filters selected by user', $responseArray);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }

    public function save_user_domains(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'domain_ids' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'No domain ids were given', $validator->errors());
            }
            foreach ($request->domain_ids as $domain) {
                $domain_user = new DomainUser();
                $domain_user->user_id = Auth::user()->id;
                $domain_user->domain_id = $domain;
                $domain_user->save();
            }

            return $this->apiResponse->sendResponse(200, 'User domain added', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_user_domains()
    {
        try {
            $userdomains = DomainUser::with('domains')->where('user_id', Auth::user()->id)->get();
            if (count($userdomains) > 0) {
                return $this->apiResponse->sendResponse(200, 'Success', $userdomains);
            }
            return $this->apiResponse->sendResponse(404, 'No Domain linked with user', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_all_domains()
    {
        try {
            $domains = Domain::all();
            if (count($domains) > 0) {
                return $this->apiResponse->sendResponse(200, 'Success', $domains);
            }
            return $this->apiResponse->sendResponse(404, 'No Domains found.', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_all_countries(Request $request)
    {
        $countries = Country::all();
        return $this->apiResponse->sendResponse(200, 'All countries fetched.', $countries);
    }

    public function get_location($location_id)
    {
        try {
            $country = DB::table('opportunity_locations')->select('location')->where('id', $location_id)->get();
            return $this->apiResponse->sendResponse(200, 'Success', $country);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }

    public function get_funding_status($id)
    {
        try {
            $fund_status = DB::table('fund_types')->select('type')->where('id', $id)->get();
            return $this->apiResponse->sendResponse(200, 'Success', $fund_status);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }

    public function get_user_language()
    {
        try {
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
            } else {
                return $this->apiResponse->sendResponse(500, 'Un Authorized', null);
            }

            $lang = DB::table('user_details')->select('language_id')->where('user_id', $user->id)->get();
            return $this->apiResponse->sendResponse(200, 'Success', $lang);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }

    public function get_user_filters()
    {
        try {
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthenticated', null);
            }

            $tags_json = DB::table('tag_user')->select('tag_id')->where('user_id', $user->id)->get();
            $tags = [];
            foreach ($tags_json as $tag) {
                $tags[] = $tag;
            }
            $tags_processed = [];
            foreach ($tags as $tag) {
                $tags_processed[] = $tag->tag_id;
            }

            return $this->apiResponse->sendResponse(200, 'Success', $tags_processed);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }

    public function segment_analytics(Request $request)
    {

        try {
            // Create new Analytics, connect it to user, connect it to opportunity
            $user = User::find($request->userId);
            $is_view_action = strcmp($request->event, "Views");

            $action = Action::where('event', $request->event)->first();

            if (is_null($user) || is_null($action))
                return $this->apiResponse->sendResponse(200, 'Successfully added analytics', null);

            if (array_key_exists("opp_id", $request->properties)) {
                $opportunity = Opportunity::find($request->properties["opp_id"]);
            } else {
                $opportunity = null;
            }

            if ($is_view_action == 0) {
                if (!is_null($opportunity)) {
                    if ($opportunity->views()->whereDate('created_at', '=', Carbon::today()->toDateString())->exists()) {
                        $opportunity->views()->whereDate('created_at', '=', Carbon::today()->toDateString())->increment('views');
                    } else {
                        $opportunity->views()->create([1]);
                    }
                    $opportunity->save();
                }

                if ($request->properties["duration"] > 5000) {
                    $analytics = new Analytics;
                    $analytics->key = "duration";
                    $analytics->value = $request->properties["duration"];
                    $analytics->action()->associate($action);
                    $analytics->user()->associate($user);
                    if (!is_null($opportunity))
                        $analytics->opportunity()->associate($opportunity);

                    $analytics->save();
                }
            } else {
                foreach ($request->properties as $key => $val) {
                    if (strcmp($key, "opp_id") == 0) {
                        if (count($request->properties) == 1) {
                            $analytics = new Analytics;
                            $analytics->action()->associate($action);
                            $analytics->user()->associate($user);
                            if (!is_null($opportunity))
                                $analytics->opportunity()->associate($opportunity);

                            $analytics->save();
                        }
                        continue;
                    }
                    $analytics = new Analytics;
                    $analytics->key = $key;
                    $analytics->value = $val;
                    $analytics->action()->associate($action);
                    $analytics->user()->associate($user);
                    if (!is_null($opportunity))
                        $analytics->opportunity()->associate($opportunity);

                    $analytics->save();
                }
            }
            return $this->apiResponse->sendResponse(200, 'Successfully added analytics', null);
        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    function razorpay_demo_checkout(request $request)
    {
        $validator = Validator::make($request->all(), [
            "payment_id" => "required",
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {

            // Set Variables
            $api = new Api(env('RAZOR_KEY'), env('RAZOR_SECRET'));

            // Get Payment Details
            $payment = $api->payment->fetch($request->payment_id);

//            $mentor_detail = MentorDetail::where('user_id', $request->mentor_id)->first();
//
//            if (!$mentor_detail) {
//                return $this->apiResponse->sendResponse(400, 'Mentor does not exist', null);
//            }

            if (!$payment) {
                return $this->apiResponse->sendResponse(400, 'Payment ID is invalid', null);
            }

            // Capture the payment
            if ($payment->status == 'authorized') {

                // Capturing Payment
                $payment->capture(
                    array('amount' => $payment->amount, 'currency' => $payment->currency)
                );
                // Create A TXN
//                $txn = new Transaction();
//                $txn->transaction_id = $payment->id;
//                $txn->user_id = Auth::id();
//                $txn->mentor_id = $request->mentor_id;
//                $txn->product_id = 3;
//                $txn->valid = 1;
//                $txn->save();



//                $mentor_detail->num_paid_subscribers = $mentor_detail->num_paid_subscribers + 1;
//                $mentor_detail->save();

                return $this->apiResponse->sendResponse(200, 'Purchase Successful.', null);
            } else if ($payment->status == 'refunded') {
                // Payment was refunded
                return $this->apiResponse->sendResponse(400, 'Transaction was refunded', null);
            } else if ($payment->status == 'failed') {
                // Payment Failed
                return $this->apiResponse->sendResponse(400, 'Transaction was failed', null);
            } else if ($payment->status == 'captured') {
                // Payment Token Already used
                return $this->apiResponse->sendResponse(400, 'Transaction was already captured', null);
            } else {
                // Unkown Error
                return $this->apiResponse->sendResponse(400, 'Transaction not captured', null);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(400, 'Payment Error', $e->getMessage());
        }
    }

}
