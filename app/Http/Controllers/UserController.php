<?php

namespace App\Http\Controllers;

use App\Country;
use App\Language;
use App\User;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Domain;
use App\DomainUser;
use App\EducationStandard;
use App\EducationUser;
use App\Institute;
use App\State;
use App\UserCertificate;
use File;
use Illuminate\Support\Facades\Config;

class UserController extends Controller
{
    private $apiResponse;
    // private $aws_base_url = "https://precisely-test1221001-dev.s3.ap-south-1.amazonaws.com";

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function get_current_user_details($user_id)
    {

        return User::with('certificates', 'domains', 'education_standard.institute_name', 'education_standard.standard_name')->where('id', $user_id)->get();
    }

    public function get_user_from_slug(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }
        try {
            $user = User::where('slug', $request->slug)->first();
            if ($user) {
                $user_profile = $this->get_current_user_details($user->id);
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'User profile get successfully', $user_profile);
            }

            DB::commit();
            return $this->apiResponse->sendResponse(404, 'User not found', null);
            DB::commit();
            return $this->apiResponse->sendResponse(401, 'Unauthorize User', null);
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function submit_user_profile(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Auth::check()) {
                $check_validation = array(
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users',
                    'age' => 'required|int',
                    'country' => 'required|integer|min:1|max:' . Country::count(),
                    'state'   => 'required|string',
                    'profile_link' => 'string',
                    'skill_name' => 'sometimes|array',
                    'short_bio' => 'sometimes|string',
                    'phone' => 'integer',
                    'date_of_birth' => 'required|date_format:d-m-Y',
                    'facebook_link' => 'sometimes|string',
                    'instagram_link' => 'sometimes|string',
                    'github_link' => 'sometimes|string',
                    'twitter_url' => 'sometimes|string',
                    'linkedin_url' => 'sometimes|string',
                    'avatar' => 'sometimes|mimes:jpeg,png,jpg|max:1024',
                    'certificate_description' => 'string|max:500',
                    'certificate_organization' => 'string',
                    'education_institute' => 'sometimes|array',
                );

                if ($request->skill_name) {
                    $check_validation['skill_experience'] = 'required|array|min:' . count($request->skill_name) . '|max:' . count($request->skill_name);
                    $check_validation['skill_experience.*'] = 'integer|between:1,5';
                }
                if ($request->file('certificate_image')) {
                    $check_validation['certificate_issuing_date'] = 'required|date_format:d-m-Y';
                    $check_validation['certificate_image.*'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';
                }
                if ($request->education_institute) {
                    $check_validation['education_standard'] = 'required|array|min:' . count($request->education_institute) . '|max:' . count($request->education_institute);
                }

                $validator = Validator::make($request->all(), $check_validation);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }

                $user = Auth::user();
                // Save data to users table
                $user->name = $request->name;
                //                $user->email = $request->email;
                //                $user->unique_id = uniqid();


                if ($request->file('avatar')) {
                    $attachment = $request->file('avatar');
                    $storage_path = 'user/profile/';
                    $imgpath = commonUploadFile($storage_path, $attachment);
                    $user->avatar = env('BASE_URL') . $imgpath;
                }

                if (isset($request->profile_link)) {
                    $user->profile_link = $request->profile_link;
                }

                if (isset($request->facebook_link)) {
                    $user->facebook_link = $request->facebook_link;
                }

                if (isset($request->instagram_link)) {
                    $user->instagram_link = $request->instagram_link;
                }

                if (isset($request->github_link)) {
                    $user->github_link = $request->github_link;
                }

                if (isset($request->twitter_url)) {
                    $user->twitter_url = $request->twitter_url;
                }

                if (isset($request->linkedin_url)) {
                    $user->linkedin_url = $request->linkedin_url;
                }

                if (isset($request->phone)) {
                    $user->phone = $request->phone;
                }

                if (isset($request->short_bio)) {
                    $user->short_bio = $request->short_bio;
                }

                $state = State::firstOrNew(array('name' =>  strtolower($request->state)));
                $state->save();

                $user->language_id = Language::where('code', Config::get('app.locale'))->first()->id;
                $slug = str_replace(" ", "-", strtolower($request->name)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 5);
                $user->slug = $slug;
                $user->age = $request->age;
                $user->country_id = $request->country;
                $user->state_id = $state->id;
                $user->dob = date('Y-m-d', strtotime($request->date_of_birth));
                $user->flag = 1;
                $user->save();

                if ($request->file('certificate_image')) {
                    $attachment = $request->file('certificate_image');
                    $storage_path = 'user/certificates/';
                    $imgpath = commonUploadFile($storage_path, $attachment);

                    $user_certificate = new UserCertificate();
                    $user_certificate->user_id = $user->id;
                    $user_certificate->image = env('BASE_URL') . $imgpath;
                    if (isset($request->certificate_description)) {
                        $user_certificate->description = $request->certificate_description;
                    }
                    if (isset($request->certificate_organization)) {
                        $user_certificate->organization = $request->certificate_organization;
                    }
                    $user_certificate->issuing_date = $request->certificate_issuing_date;
                    $user_certificate->save();
                }

                if (isset($request->skill_name) && isset($request->skill_experience)) {
                    foreach ($request->skill_name as $key => $domain) {
                        $domainCheck = Domain::where('name', strtolower($domain))->first();
                        if ($domainCheck) {
                            $user->domains()->attach($domainCheck->id, array('experience' => $request->skill_experience[$key]));
                        } else {
                            $domainNew = new Domain();
                            $domainNew->name = strtolower($domain);
                            $domainNew->save();
                            $user->domains()->attach($domainNew->id, array('experience' => $request->skill_experience[$key]));
                        }
                    }
                }

                if (isset($request->education_institute) && isset($request->education_standard)) {
                    $this->common_education_standard($request, $user->id);
                }

                $user = $this->get_current_user_details($user->id);
                //$user = User::with('certificates', 'domains', 'education_standard.institute_name', 'education_standard.standard_name')->where('id', $user->id)->get();

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'User details saved', $user);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function update_user_profile(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Auth::check()) {
                $check_validation = array(
                    'name' => 'sometimes|string|max:255',
                    'age' => 'sometimes|int',
                    'country' => 'sometimes|integer|min:1|max:' . Country::count(),
                    'state'   => 'sometimes|string',
                    'profile_link' => 'string',
                    'short_bio' => 'sometimes|string',
                    'date_of_birth' => 'sometimes|date_format:d-m-Y',
                    'phone' => 'integer',
                    'skill_name' => 'sometimes|array',
                    'facebook_link' => 'sometimes|string',
                    'instagram_link' => 'sometimes|string',
                    'github_link' => 'sometimes|string',
                    'twitter_url' => 'sometimes|string',
                    'linkedin_url' => 'sometimes|string',
                    'avatar' => 'sometimes|mimes:jpeg,png,jpg|max:1024',
                    'certificate_description' => 'string|max:500',
                    'certificate_organization' => 'string',
                    'education_institute' => 'sometimes|array',
                );

                if ($request->skill_name) {
                    $check_validation['skill_experience'] = 'required|array|min:' . count($request->skill_name) . '|max:' . count($request->skill_name);
                    $check_validation['skill_experience.*'] = 'integer|between:1,5';
                }
                if ($request->file('certificate_image')) {
                    $check_validation['certificate_issuing_date'] = 'required|date_format:d-m-Y';
                    $check_validation['certificate_image.*'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';
                }
                if ($request->education_institute) {
                    $check_validation['education_standard'] = 'required|array|min:' . count($request->education_institute) . '|max:' . count($request->education_institute);
                }

                $validator = Validator::make($request->all(), $check_validation);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }

                $user = Auth::user();
                $user->language_id = Language::where('code', Config::get('app.locale'))->first()->id;

                if (isset($request->name)) {
                    $user->name = $request->name;
                    $slug = str_replace(" ", "-", strtolower($request->name)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 5);
                    $user->slug = $slug;
                }

                if ($request->file('avatar')) {
                    $attachment = $request->file('avatar');
                    $storage_path = 'user/profile/';
                    $imgpath = commonUploadFile($storage_path, $attachment);
                    $user->avatar = env('BASE_URL') . $imgpath;
                }

                if (isset($request->profile_link))
                    $user->profile_link = $request->profile_link;

                if (isset($request->facebook_link)) {
                    $user->facebook_link = $request->facebook_link;
                }

                if (isset($request->instagram_link)) {
                    $user->instagram_link = $request->instagram_link;
                }

                if (isset($request->github_link)) {
                    $user->github_link = $request->github_link;
                }

                if (isset($request->twitter_url)) {
                    $user->twitter_url = $request->twitter_url;
                }

                if (isset($request->linkedin_url)) {
                    $user->linkedin_url = $request->linkedin_url;
                }

                if (isset($request->phone))
                    $user->phone = $request->phone;

                if (isset($request->age))
                    $user->age = $request->age;

                if (isset($request->country))
                    $user->country_id = $request->country;

                if (isset($request->short_bio))
                    $user->short_bio = $request->short_bio;

                if (isset($request->state)) {
                    $state = State::firstOrNew(array('name' =>  strtolower($request->state)));
                    $state->save();
                    $user->state_id = $state->id;
                }

                if (isset($request->date_of_birth)) {
                    $user->dob = date('Y-m-d', strtotime($request->date_of_birth));
                }

                $user->save();

                if ($request->file('certificate_image')) {
                    $attachment = $request->file('certificate_image');
                    $storage_path = 'user/certificates/';
                    $imgpath = commonUploadFile($storage_path, $attachment);

                    $user_certificate = new UserCertificate();
                    $user_certificate->user_id = $user->id;
                    $user_certificate->image = env('BASE_URL') . $imgpath;
                    if (isset($request->certificate_description)) {
                        $user_certificate->description = $request->certificate_description;
                    }
                    if (isset($request->certificate_organization)) {
                        $user_certificate->organization = $request->certificate_organization;
                    }
                    $user_certificate->issuing_date = $request->certificate_issuing_date;
                    $user_certificate->save();
                }

                if (isset($request->skill_name) && isset($request->skill_experience)) {
                    foreach ($request->skill_name as $key => $domain) {
                        $domainCheck = Domain::where('name', strtolower($domain))->first();
                        if ($domainCheck) {
                            $user->domains()->attach($domainCheck->id, array('experience' => $request->skill_experience[$key]));
                        } else {
                            $domainNew = new Domain();
                            $domainNew->name = strtolower($domain);
                            $domainNew->save();
                            $user->domains()->attach($domainNew->id, array('experience' => $request->skill_experience[$key]));
                        }
                    }
                }

                if (isset($request->education_institute) && isset($request->education_standard)) {
                    $this->common_education_standard($request, $user->id);
                }

                $user = $this->get_current_user_details($user->id);
                //$user = User::with('certificates', 'domains', 'education_standard.institute_name', 'education_standard.standard_name')->where('id', $user->id)->get();

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'User details Updated', $user);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    private function common_education_standard(Request $request, $user_id)
    {
        if (isset($request->education_institute) && isset($request->education_standard)) {
            $institutes = array();
            foreach ($request->education_institute as $key => $insti) {
                $institute = Institute::where('name', strtolower($insti))->first();
                if (!$institute) {
                    $institute = new Institute();
                    $institute->name = strtolower($insti);
                    $institute->save();
                }
                array_push($institutes, $institute->id);
            }

            $education_standards = array();
            foreach ($request->education_standard as $key => $education) {
                $education_standard = EducationStandard::where('name', strtolower($education))->first();
                if (!$education_standard) {
                    $education_standard = new EducationStandard();
                    $education_standard->name = strtolower($education);
                    $education_standard->save();
                }
                array_push($education_standards, $education_standard->id);
            }

            for ($i = 0; $i < count($institutes); $i++) {
                $alreadyExists = EducationUser::where(['user_id' => $user_id, 'institutes_id' => $institutes[$i], 'education_standard_id' => $education_standards[$i]])->first();
                if (!$alreadyExists) {
                    $add_education_institute = new EducationUser();
                    $add_education_institute->institutes_id = $institutes[$i];
                    $add_education_institute->education_standard_id = $education_standards[$i];
                    $add_education_institute->user_id = $user_id;
                    $add_education_institute->save();
                }
            }
        }
        return true;
    }

    public function add_user_education(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Auth::check()) {
                $validator = Validator::make($request->all(), [
                    'education_institute' => 'required|array',
                    'education_standard' => 'required|array|min:' . count($request->education_institute) . '|max:' . count($request->education_institute),
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }

                $user_id = Auth::user()->id;
                $this->common_education_standard($request, $user_id);
                $user = $this->get_current_user_details($user_id);
                //                $user = User::with('certificates', 'domains', 'education_standard.institute_name', 'education_standard.standard_name')->where('id', Auth::user()->id)->get();
                //                $this->common_education_standard($request, Auth::user()->id);

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'User Education Saved', $user);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_user_certificate(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Auth::check()) {
                $validator = Validator::make($request->all(), [
                    'certificate_image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'certificate_description' => 'string|max:500',
                    'certificate_organization' => 'string',
                    'certificate_issuing_date' => 'required|date_format:d-m-Y',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }

                if ($request->file('certificate_image')) {
                    $image = $request->file('certificate_image');
                    $attachment = $image;
                    $storage_path = 'user/certificates/';
                    $imgpath = commonUploadFile($storage_path, $attachment);

                    $user_certificate = new UserCertificate();
                    $user_certificate->user_id = Auth::user()->id;
                    $user_certificate->image = env('BASE_URL') . $imgpath;
                    if (isset($request->certificate_description)) {
                        $user_certificate->description = $request->certificate_description;
                    }
                    if (isset($request->certificate_organization)) {
                        $user_certificate->organization = $request->certificate_organization;
                    }
                    $user_certificate->issuing_date = $request->certificate_issuing_date;
                    $user_certificate->save();
                }
                $user = $this->get_current_user_details(Auth::user()->id);
                //$user = User::with('certificates', 'domains')->where('id', Auth::user()->id)->get();
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'User Certificates Added Successfully', $user);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function update_user_certificate(Request $request)
    {
        DB::beginTransaction();
        try {
            $check_validation = array(
                'certificate_id'           => 'required|integer',
                'certificate_image'        => 'sometimes|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'certificate_description'  => 'string|max:500',
                'certificate_organization' => 'string',
            );

            if ($request->file('certificate_image')) {
                $check_validation['certificate_issuing_date'] = 'required|date_format:d-m-Y';
                $check_validation['certificate_image.*'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
            }

            $validator = Validator::make($request->all(), $check_validation);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $find_certificate = UserCertificate::where(['id' => $request->certificate_id, 'user_id' => Auth::user()->id])->first();
            if ($find_certificate) {
                if ($request->file('certificate_image')) {
                    $attachment = $request->file('certificate_image');
                    $storage_path = 'user/certificates/';
                    $imgpath = commonUploadFile($storage_path, $attachment);

                    $find_certificate->image = env('BASE_URL') . $imgpath;
                }
                if (isset($request->certificate_description)) {
                    $find_certificate->description = $request->certificate_description;
                }
                if (isset($request->certificate_organization)) {
                    $find_certificate->organization = $request->certificate_organization;
                }
                if (isset($request->certificate_issuing_date)) {
                    $find_certificate->issuing_date = $request->certificate_issuing_date;
                }
                $find_certificate->save();

                $user = $this->get_current_user_details(Auth::user()->id);
                // $user = User::with('certificates', 'domains', 'education_standard.institute_name', 'education_standard.standard_name')->where('id', Auth::user()->id)->get();
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'User Certificates Updated Successfully', $user);
            } else {
                return $this->apiResponse->sendResponse(404, 'User Certificate Not Found or Unauthorized User', null);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function update_user_skill(Request $request)
    {
        DB::beginTransaction();
        try {
            $check_validation = array(
                'skill_ids' => 'required|array',
            );

            if ($request->skill_ids) {
                $check_validation['skill_names'] = 'required|array|min:' . count($request->skill_ids) . '|max:' . count($request->skill_ids);
                $check_validation['skill_names.*'] = 'string';
                $check_validation['skill_experiences'] = 'required|array|min:' . count($request->skill_ids) . '|max:' . count($request->skill_ids);
                $check_validation['skill_experiences.*'] = 'integer|between:1,5';
            }

            $validator = Validator::make($request->all(), $check_validation);

            // $validator = Validator::make($request->all(), [
            //     'skill_id'           => 'required|integer',
            //     'skill_name'         => 'sometimes|string',
            //     'skill_experience'   => 'sometimes|integer|between:1,5',
            // ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            if (isset($request->skill_ids) && isset($request->skill_names) && isset($request->skill_experiences)) {
                foreach ($request->skill_ids as $key => $skill_id) {
                    $domain_find = Domain::find($skill_id);
                    if ($domain_find) {
                        $old_domain = DomainUser::where(['user_id' => Auth::user()->id, 'domain_id' => $skill_id])->first();
                        $domain_id = Domain::where('name', strtolower($request->skill_names[$key]))->first();
                        if (!$domain_id) {
                            $domain_id = new Domain();
                            $domain_id->name = strtolower($request->skill_names[$key]);
                            $domain_id->save();
                        }

                        if (isset($request->skill_experiences[$key])) {
                            $experience = $request->skill_experiences[$key];
                        } else {
                            $experience = $old_domain->experience;
                        }
                        $old_domain->domain_id = $domain_id->id;
                        $old_domain->experience = $experience;
                        $old_domain->save();
                    }
                }
                $user = $this->get_current_user_details(Auth::user()->id);
                // $user = User::with('certificates', 'domains', 'education_standard.institute_name', 'education_standard.standard_name')->where('id', Auth::user()->id)->get();
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'User Skill Updated Successfully', $user);
            } else {
                return $this->apiResponse->sendResponse(404, 'User Skill Not Found or Unauthorized User', null);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function update_user_education_institute(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Auth::check()) {
                $check_validation = array(
                    'education_institute_ids' => 'required|array',
                );

                if ($request->education_institute_ids) {
                    $check_validation['education_institutes'] = 'required|array|min:' . count($request->education_institute_ids) . '|max:' . count($request->education_institute_ids);
                }

                $validator = Validator::make($request->all(), $check_validation);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }

                if (isset($request->education_institute_ids) && isset($request->education_institutes)) {
                    foreach ($request->education_institute_ids as $key => $insti_id) {
                        $domainCheck = EducationUser::where(['user_id' => Auth::user()->id, 'institutes_id' => $insti_id])->first();
                        if ($domainCheck) {
                            $institute = Institute::where('name', strtolower($request->education_institutes[$key]))->first();
                            if ($institute) {
                                $domainCheck->institutes_id = $institute->id;
                            } else {
                                $new_institute = new Institute();
                                $new_institute->name = strtolower($request->education_institutes[$key]);
                                $new_institute->save();
                                $domainCheck->institutes_id = $new_institute->id;
                            }
                            $domainCheck->save();
                        }
                    }
                } else {
                    DB::commit();
                    return $this->apiResponse->sendResponse(400, 'Requested Parameter Values Missing', null);
                }

                $user = $this->get_current_user_details(Auth::user()->id);
                // $user = User::with('certificates', 'domains', 'education_standard.institute_name', 'education_standard.standard_name')->where('id', Auth::user()->id)->get();

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'User Institute Update Successfully', $user);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function update_user_education_standard(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Auth::check()) {
                $check_validation = array(
                    'education_standard_ids' => 'required|array',
                );

                if ($request->education_standard_ids) {
                    $check_validation['education_standards'] = 'required|array|min:' . count($request->education_standard_ids) . '|max:' . count($request->education_standard_ids);
                }

                $validator = Validator::make($request->all(), $check_validation);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }

                if (isset($request->education_standard_ids) && isset($request->education_standards)) {
                    foreach ($request->education_standard_ids as $key => $standard_id) {
                        $standardCheck = EducationUser::where(['user_id' => Auth::user()->id, 'education_standard_id' => $standard_id])->first();
                        if ($standardCheck) {
                            $standard = EducationStandard::where('name', strtolower($request->education_standards[$key]))->first();
                            if ($standard) {
                                $standardCheck->education_standard_id = $standard->id;
                            } else {
                                $new_standard = new EducationStandard();
                                $new_standard->name = strtolower($request->education_standards[$key]);
                                $new_standard->save();
                                $standardCheck->education_standard_id = $new_standard->id;
                            }
                            $standardCheck->save();
                        }
                    }
                } else {
                    DB::commit();
                    return $this->apiResponse->sendResponse(400, 'Requested Parameter Values Missing', null);
                }

                $user = $this->get_current_user_details(Auth::user()->id);
                // $user = User::with('certificates', 'domains', 'education_standard.institute_name', 'education_standard.standard_name')->where('id', Auth::user()->id)->get();

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'User Education Standard Update Successfully', $user);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_user_profile()
    {
        DB::beginTransaction();
        if (Auth::check()) {
            //$user = Auth::user();
            $user = $this->get_current_user_details(Auth::user()->id);
            //$user = User::with('certificates', 'domains')->where('id', Auth::user()->id)->get();
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Successfully fetched user profile.', $user);
        } else {
            return $this->apiResponse->sendResponse(500, 'User profile not complete', null);
        }
    }

    public function old_user_slug_generate(Request $request)
    {
        DB::beginTransaction();
        try {
            //            if (Auth::user()->role_id == 3) {
            $find_users = User::where('slug', null)->get();
            if (count($find_users) > 0) {
                foreach ($find_users as $key => $user) {
                    $current_user = User::find($user->id);
                    $slug = str_replace(" ", "-", strtolower($current_user->name)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 5);
                    $current_user->slug = $slug;
                    $current_user->save();
                }
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Slug Genereted Successfully', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(404, 'User not found without slug', null);
            //            }
            //            DB::commit();
            //            return $this->apiResponse->sendResponse(401, 'Only Admin use this api', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function delete_certificate(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'certificate_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            $find_certificate = UserCertificate::where(['id' => $request->certificate_id, 'user_id' => Auth::user()->id])->first();

            if (!$find_certificate) {
                DB::commit();
                return $this->apiResponse->sendResponse(404, 'Certificate Not Found or Unauthorized User', null);
            }

            $goodUrl = str_replace('https://api.learnwithyoutube.org/', '', $find_certificate->image);

            File::delete($goodUrl);
            $find_certificate->delete();
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'User Certificate Deleted Successfully', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function delete_skill(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'domain_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            $find_domain = DomainUser::where(['domain_id' => $request->domain_id, 'user_id' => Auth::user()->id])->first();

            if (!$find_domain) {
                DB::commit();
                return $this->apiResponse->sendResponse(404, 'Skill Not Found or Unauthorized User', null);
            }

            $find_domain->delete();
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'User Skill Deleted Successfully', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function delete_education(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'education_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            $find_education = EducationUser::where(['id' => $request->education_id, 'user_id' => Auth::user()->id])->first();

            if (!$find_education) {
                DB::commit();
                return $this->apiResponse->sendResponse(404, 'Education Not Found or Unauthorized User', null);
            }

            $find_education->delete();
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'User Education Deleted Successfully', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_user_facebook_link(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Auth::check()) {
                $validator = Validator::make($request->all(), [
                    'facebook_link' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }
                $user_link = Auth::user();
                $user_link->facebook_link = $request->facebook_link;
                $user_link->save();
                $user = $this->get_current_user_details(Auth::user()->id);
                //$user = User::with('certificates', 'domains')->where('id', Auth::user()->id)->get();
                DB::commit();

                return $this->apiResponse->sendResponse(200, 'User Facebook Link Added Successfully', $user);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_user_instagram_link(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Auth::check()) {
                $validator = Validator::make($request->all(), [
                    'instagram_link' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }
                $user_link = User::find(Auth::user()->id);
                $user_link->instagram_link = $request->instagram_link;
                $user_link->save();
                $user = $this->get_current_user_details(Auth::user()->id);
                //$user = User::with('certificates', 'domains')->where('id', Auth::user()->id)->get();
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'User Instagram Link Added Successfully', $user);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_user_github_link(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Auth::check()) {
                $validator = Validator::make($request->all(), [
                    'github_link' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }
                $user_link = User::find(Auth::user()->id);
                $user_link->github_link = $request->github_link;
                $user_link->save();
                $user = $this->get_current_user_details(Auth::user()->id);
                //$user = User::with('certificates', 'domains')->where('id', Auth::user()->id)->get();
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'User GitHub Link Added Successfully', $user);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_user_twitter_url(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Auth::check()) {
                $validator = Validator::make($request->all(), [
                    'twitter_url' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }
                $user_link = User::find(Auth::user()->id);
                $user_link->twitter_url = $request->twitter_url;
                $user_link->save();
                $user = $this->get_current_user_details(Auth::user()->id);
                //$user = User::with('certificates', 'domains')->where('id', Auth::user()->id)->get();
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'User Twitter Link Added Successfully', $user);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_user_linkedin_url(Request $request)
    {
        DB::beginTransaction();
        try {
            if (Auth::check()) {
                $validator = Validator::make($request->all(), [
                    'linkedin_url' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }
                $user_link = User::find(Auth::user()->id);
                $user_link->linkedin_url = $request->linkedin_url;
                $user_link->save();
                $user = $this->get_current_user_details(Auth::user()->id);
                //                $user = User::with('certificates', 'domains')->where('id', Auth::user()->id)->get();
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'User LinkedIn Link Added Successfully', $user);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}
