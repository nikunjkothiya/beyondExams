<?php

namespace App\Http\Controllers;

use App\Category;
use App\Comment;
use App\Country;
use App\Language;
use App\LearningPath;
use App\Role;
use App\UserHistory;
use App\User;
use App\Video;
use App\BookmarkVideo;
use App\HistoryUserVidoes;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Search;
use App\VideoRating;
use App\AttemptTest;
use App\CategoryUserEnrollment;
use App\CategoryUserRating;
use App\Domain;
use App\DomainUser;
use App\EducationStandard;
use App\EducationUser;
use App\Institute;
use App\Keyword;
use App\KeywordUser;
use App\KeywordVideo;
use App\State;
use App\UserCertificate;
use App\VideoAnnotation;
use File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Config;

class LearnWithYoutubeController extends Controller
{
    private $apiResponse;
    // private $aws_base_url = "https://precisely-test1221001-dev.s3.ap-south-1.amazonaws.com";

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function submit_feedback(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'email' => 'string',
            'message' => 'string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        $name = '';
        if ($request->name)
            $name = $request->name;

        $email = '';
        if ($request->email)
            $email = $request->email;

        DB::table('feedbacks')->insert(['name' => $name, 'email' => $email, 'message' => $request->message]);
        DB::commit();
        return $this->apiResponse->sendResponse(200, 'Feedback saved successfully', null);
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
            if (Auth::user()->role_id == 3) {
                $find_users = User::where('slug', null)->get();
                if(count($find_users) > 0){
                    foreach($find_users as $key=>$user){
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
            }
            DB::commit();
            return $this->apiResponse->sendResponse(401, 'Only Admin use this api', null);
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

    public function addNewCategory(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'level' => 'required|integer',
            'parent_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            if (Auth::user()->role_id == 2) {
                $find = Category::where('title', strtolower($request->title))->first();
                if (!$find) {
                    $description = ($request->description) ? $request->description : null;

                    $slug = str_replace(" ", "-", strtolower($request->title)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 5);

                    if ($request->image) {
                        $cate_image = $request->file('image');
                        $storage_path = 'category/images/';
                        $imgpath = commonUploadFile($storage_path, $cate_image);
                        $full_imgpath = env('BASE_URL') . $imgpath;
                    }

                    $image = ($request->image) ? $full_imgpath : null;
                    $category = Category::create(['user_id' => Auth::user()->id, 'title' => $request->title, 'description' => $description, 'image_url' => $image, 'level' => $request->level, 'parent_id' => $request->parent_id, 'slug' => $slug]);
                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'New Category added', $category);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(201, 'Already Category have', $find);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(401, 'Only Teacher can add category', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function edit_category(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'title' => 'string',
            'description' => 'string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            if (Auth::user()->role_id == 2) {

                $findCategory = Category::where(['id' => $request->category_id, 'user_id' => Auth::user()->id])->first();
                if ($findCategory) {
                    if ($request->title) {
                        $findCategory->title = $request->title;
                        $slug = str_replace(" ", "-", strtolower($request->title)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 5);
                        $findCategory->slug = $slug;
                    }
                    if ($request->description) {
                        $findCategory->description = $request->description;
                    }

                    if ($request->image) {
                        $cate_image = $request->file('image');
                        $storage_path = 'category/images/';
                        $imgpath = commonUploadFile($storage_path, $cate_image);
                        $full_imgpath = env('BASE_URL') . $imgpath;
                        $image = ($request->image) ? $full_imgpath : $findCategory->image_url;
                        $findCategory->image_url = $image;
                    }

                    $findCategory->save();

                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Category Update Successfully', $findCategory);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(404, 'Category not found', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(401, 'Only Teacher can add category', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_category_rating(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
            'rating' => 'required|integer|between:1,5',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            $find_category = Category::find($request->category_id);
            if ($find_category) {
                $already = CategoryUserRating::where(['user_id' => Auth::user()->id, 'category_id' => $request->category_id])->first();
                if (!$already) {
                    $find_category->rating_sum += $request->rating;
                    $find_category->rated_user += 1;
                    $find_category->save();

                    Auth::user()->categoryRating()->attach($find_category->id, array('rating' => $request->rating));
                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Category Rating Added Successfully', $find_category);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Already Added Rating', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(404, 'Category Not Found', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_category_rating(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            $find_category = Category::find($request->category_id);
            DB::commit();

            if ($find_category) {
                $total_rating['id'] = $find_category->id;
                $total_rating['average_rating'] = round($find_category->rating_sum / $find_category->rated_user);
                return $this->apiResponse->sendResponse(200, 'Get Category Rating Successfully', $total_rating);
            }

            return $this->apiResponse->sendResponse(404, 'Category Not Found', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_category_enrollment(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            $find_category = Category::find($request->category_id);
            if ($find_category) {
                $already = CategoryUserEnrollment::where(['user_id' => Auth::user()->id, 'category_id' => $request->category_id])->first();
                if (!$already) {
                    Auth::user()->categoryEnrollment()->attach($request->category_id);
                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Category Enrollment Successfully', null);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Already Enrolled Category', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(404, 'Category Not Found', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_category_enrollment(Request $request)
    {
        DB::beginTransaction();

        try {
            $enrolled_categories = CategoryUserEnrollment::with('categories')->where('user_id', Auth::user()->id)->get();
            return $this->apiResponse->sendResponse(200, 'Get Enrolled Category Successfully', $enrolled_categories);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function getNextLevel(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'level' => 'required|integer',
            'parent_id' => 'integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        if ($request->level > 1) {
            if (!$request->parent_id) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing.', 'parent_id parameter not included');
            }
            $parent_id = $request->parent_id;
        } else {
            $parent_id = 0;
        }

        $categories = Category::with('user:id,name,avatar,slug')->where('level', $request->level)->where('parent_id', $parent_id)->get();
        if (count($categories) == 0) {
            $request->request->add(['category_id' => $parent_id]);
            return $this->get_learning_path($request);
        }
        DB::commit();
        return $this->apiResponse->sendResponse(200, 'Categories fetched successfully', $categories);
    }

    public function get_learning_path(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|int',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        return $this->apiResponse->sendResponse(200, 'Learning path fetched successfully', LearningPath::with('video')->where('category_id', $request->category_id)->orderBy('ordering', 'asc')->get());
    }

    public function getAllCategories(Request $request)
    {
        if ($request->role_id && $request->role_id == 3)
            return $this->apiResponse->sendResponse(200, 'Categories fetched successfully', $categories = Category::get());
        return $this->apiResponse->sendResponse(200, 'Categories fetched successfully', $categories = Category::where('visibility', 1)->get());
    }

    public function getAllCategoriesHierarchically(Request $request)
    {
        if ($request->role_id && $request->role_id == 3)
            $categories = Category::get();
        else
            $categories = Category::where('visibility', 1)->get();
        $tree = function ($elements, $parentId = 0) use (&$tree) {
            $branch = array();
            foreach ($elements as $element) {

                if ($element['parent_id'] == $parentId) {

                    $children = $tree($elements, $element['id']);
                    if ($children) {
                        $element['children'] = $children;
                    } else {
                        // $element['children'] = [];
                    }
                    $branch[] = $element;
                }
            }

            return $branch;
        };

        $tree = $tree($categories);
        return $this->apiResponse->sendResponse(200, 'Categories fetched successfully', $tree);
    }


    public function getCategories(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'level' => 'required|integer',
            'parent_id' => 'integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        if ($request->level > 1) {
            if (!$request->parent_id) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing.', 'parent_id parameter not included');
            }
            $parent_id = $request->parent_id;
        } else {
            $parent_id = 0;
        }

        $categories = Category::where('level', $request->level)->where('parent_id', $parent_id)->get();
        DB::commit();
        return $this->apiResponse->sendResponse(200, 'Categories fetched successfully', $categories);
    }

    ///// Start removeCategory Function /////
    public function removeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {

            $category = Category::find($request->category_id);
            if (is_null($category)) {

                return $this->apiResponse->sendResponse(404, 'Category Not Found', null);
            } else {
                if ($category->user_id == Auth::user()->id) {
                    $success = $this->deleteAllCategory($category->id);
                    $learning_paths = LearningPath::whereIn('category_id', $success)->delete();
                    Category::whereIn('id', $success)->delete();

                    return $this->apiResponse->sendResponse(200, 'Category deleted successfully', null);
                } else {
                    return $this->apiResponse->sendResponse(401, 'Unauthorized user can not delete category', null);
                }
            }
        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    // for removeCategory Recursive function
    private function deleteAllCategory($id)
    {
        $theArray = array();

        $category = Category::find($id);
        array_push($theArray, $category->id);

        $toRecurses = Category::where('parent_id', $category->id)->get();
        foreach ($toRecurses as $toRecurse) {
            array_push($theArray, $toRecurse->id);
        }

        foreach ($toRecurses as $toRecurse) {
            if (Category::where('parent_id', $toRecurse->id)->get()) {
                $children = $this->deleteAllCategory($toRecurse->id);
                if ($children) {
                    $theArray[] = $children;
                }
            }
        }

        $theArray1 = $this->flatten($theArray);
        $filteredArray = array_unique($theArray1);
        $reversed = array_reverse($filteredArray);
        return $reversed;
    }

    // for deleteAllCategory Recursive function
    function flatten(array $array)
    {
        $return = array();
        array_walk_recursive($array, function ($a) use (&$return) {
            $return[] = $a;
        });

        return $return;
    }

    ///// End removeCategory Function /////

    public function get_resource_comments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'resource_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Need a resource Id', $validator->errors());
        }

        $video = Video::where('url', $request->resource_id)->first();

        if (!$video) {
            $video = new Video(['url' => $request->resource_id]);
            $video->save();
        }

        // Send notification via Notification controller function or guzzle
        return $this->apiResponse->sendResponse(200, 'Success', Comment::where('video_id', $video->id)->paginate(10));
    }

    public function get_resource_likes(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'video_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Need a resource Id', $validator->errors());
        }

        $num_likes = Video::where('url', $request->video_url)->first()->num_likes();
        // Send notification via Notification controller function or guzzle
        DB::commit();
        return $this->apiResponse->sendResponse(200, 'Success', $num_likes);
    }

    public function add_resource_comment(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'video_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $video = Video::where('url', $request->video_url)->first();

        if (!$video) {
            $video = new Video(['url' => $request->video_url]);
            $video->save();
        }

        $comment = new Comment();
        $comment->message = $request->message;
        $comment->user_id = Auth::id();
        $comment->video_id = $video->id;
        $comment->save();

        DB::commit();
        // Send notification via Notification controller function or guzzle
        return $this->apiResponse->sendResponse(200, 'Comment added successfully', $comment);
    }

    public function switch_video_like(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'video_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $video = Video::where('url', $request->video_url)->first();
        if (!$video) {
            $video = new Video(['url' => $request->video_url]);
            $video->save();
        }

        Auth::user()->videos()->toggle([array('video_id' => $video->id, 'type' => 'liked')]);
        DB::commit();
        return $this->apiResponse->sendResponse(200, 'Like Updated successfully', null);
    }

    public function addToWatchHistory(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'video_url' => 'required|string',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            if (Auth::user()) {
                $start_time = strtotime($request->start_time);
                $end_time = strtotime($request->end_time);
                $video = Video::where('url', $request->video_url)->first();
                // $user = User::find(1);
                if (!$video) {
                    $video = new Video();
                    $video->url = $request->video_url;
                    $video->save();
                }
                Auth::user()->watchHistoryVidoes()->attach($video->id, ['start_time' => $start_time, 'end_time' => $end_time, 'type' => 'history']);

                $check_history = HistoryUserVidoes::where(['user_id' => Auth::user()->id, 'video_id' => $video->id])->get();
                if (count($check_history) == 1) {
                    Video::where('id', $video->id)->update(['total_view' => $video->total_view + 1]);
                }

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Video saved to history', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function getWatchHistory()
    {
        DB::beginTransaction();
        try {
            $user_id = Auth::user()->id;
            $getHistory = Video::select('*')
                ->with('duration_history:video_id,start_time,end_time')
                ->whereHas('duration_history', function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                })->paginate();
            DB::commit();

            return $this->apiResponse->sendResponse(200, 'User watch history get successfully', $getHistory);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function getPublicHistory(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $user_id = $request->user_id;
            $check_privacy = User::where(['id' => $user_id, 'is_history_public' => 1])->first();
            if ($check_privacy) {
                $publicHistory = Video::select('*')
                    ->with('duration_history:video_id,start_time,end_time')
                    ->whereHas('duration_history', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    })->paginate();
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'User public watch history get successfully', $publicHistory);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function change_history_public(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'is_public' => 'required|numeric|between:0,1',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $change_history_privacy = User::where('id', Auth::user()->id)->update(['is_history_public' => $request->is_public]);
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'History Privacy Chnaged Successfully', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function addToSearchHistory()
    {
    }

    public function uniquelyIdentifyDevice(Request $request)
    {
    }

    public function add_video_to_learning_path(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|int',
            'video_url' => 'required|string',
            'ordering' => 'required|int',
            'start_time' => 'integer'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            if (Auth::user()->role_id == 2) {
                $video = Video::where('url', $request->video_url)->first();
                if (!$video) {
                    $video = new Video(['url' => $request->video_url]);
                    $video->save();
                }

                $findAuthorizeUser = Category::where(['user_id' => Auth::user()->id, 'id' => $request->category_id])->first();
                if ($findAuthorizeUser) {
                    $check = LearningPath::with('video')->where(['category_id' => $request->category_id, 'video_id' => $video->id])->first();
                    if ($check) {
                        DB::commit();
                        return $this->apiResponse->sendResponse(201, 'Already Video Added to this Category', $check);
                    }

                    if ($request->ordering == -1) {
                        $lp = LearningPath::where('category_id', $request->category_id)->orderBy('ordering', 'desc')->first();
                        if ($lp)
                            $ordering = $lp->ordering + 1;
                        else
                            $ordering = 1;
                    } else {
                        $ordering = $request->ordering;
                    }

                    if ($request->start_time) {
                        $start_time = $request->start_time;
                    } else {
                        $start_time = 0;
                    }

                    $new_lp_id = LearningPath::create(['user_id'=>Auth::user()->id,'category_id' => $request->category_id, 'video_id' => $video->id, 'ordering' => $ordering, 'start_time' => $start_time]);

                    $video_time = youtube_video_time_get($request->video_url); // In seconds
                    $category_find = Category::find($request->category_id);
                    $category_find->video_count += 1;
                    $category_find->total_time += $video_time;
                    $category_find->save();

                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Learning path updated', $new_lp_id);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(401, 'Unauthorize User', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(401, 'Only Teacher can add video to learning path', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function change_video_ordering(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
            'video_url' => 'required|string',
            'new_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            if (Auth::user()->id == 2) {
                $searchVideo = Video::where('url', $request->video_url)->first();
                if ($searchVideo) {
                    $find_learning_path = LearningPath::where(['user_id' => Auth::user()->id, 'category_id' => $request->category_id, 'video_id' => $searchVideo->id])->first();
                    if ($find_learning_path) {
                        $query = LearningPath::where(['user_id' => Auth::user()->id, 'category_id' => $request->category_id])->where('video_id', '!=', $searchVideo->id);

                        if ($request->new_order > $find_learning_path->ordering) {
                            $query->where('ordering', '>', $find_learning_path->ordering)->where('ordering', '<=', $request->new_order)->decrement('ordering', 1);

                            $find_learning_path->ordering = $request->new_order;
                            $find_learning_path->save();
                        } else if ($request->new_order < $find_learning_path->ordering) {
                            $query->where('ordering', '<', $find_learning_path->ordering)->where('ordering', '>=', $request->new_order)->increment('ordering', 1);

                            $find_learning_path->ordering = $request->new_order;
                            $find_learning_path->save();
                        } else {
                            DB::commit();
                            return $this->apiResponse->sendResponse(200, 'Learning path already in this order', null);
                        }

                        DB::commit();
                        return $this->apiResponse->sendResponse(200, 'Learning path ordering successfully', null);
                    }
                    DB::commit();
                    return $this->apiResponse->sendResponse(404, 'Learning path not found', null);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(404, 'Video Not Found', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(401, 'Unauthorize user', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function remove_video_from_learning_path(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|int',
            'video_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            if (Auth::user()->role_id == 2) {
                $findAuthorizeUser = Category::where(['user_id' => Auth::user()->id, 'id' => $request->category_id])->first();
                if ($findAuthorizeUser) {
                    $video_id = Video::where('url', $request->video_url)->first();
                    $check = LearningPath::where(['category_id' => $request->category_id, 'video_id' => $video_id->id])->first();

                    if (!$check) {
                        DB::commit();
                        return $this->apiResponse->sendResponse(404, 'Video in this Category Not Found', null);
                    }

                    $video_time = youtube_video_time_get($request->video_url);
                    $category_find = Category::find($request->category_id);
                    $category_find->video_count -= 1;
                    $category_find->total_time -= $video_time;
                    $category_find->save();

                    $check->delete();

                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Video Removed From Learning Path', null);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(401, 'Unauthorize User', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(401, 'Only Teacher can remove video from learning path', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function give_video_rating(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'video_url' => 'required|string',
            'rating' => 'required|integer|between:1,5',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }
        try {
            if (Auth::user()) {
                $where_video = ['url' => $request->video_url];
                $insert_video = ['url' => $request->video_url];
                $video_save = Video::updateOrCreate($where_video, $insert_video);

                $where_rating = ['user_id' => Auth::user()->id, 'video_id' => $video_save->id];
                $insert_rating = ['rating' => $request->rating];
                $video_rating = VideoRating::updateOrCreate($where_rating, $insert_rating);

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Video Rating added successfully', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function user_bookmark_video(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            if (Auth::user()) {
                $video = BookmarkVideo::where('video_id', $request->video_id)->first();
                if (!$video) {
                    Auth::user()->bookmarkVideo()->attach($video->id);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Video Bookmark successfully', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function attempt_test(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|integer',
            'test_answer' => 'required', //array
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            if (Auth::user()) {
                $where_rating = ['user_id' => Auth::user()->id, 'test_id' => $request->test_id];
                $insert_rating = ['test_answer' => $request->test_answer];
                $attempt_test = AttemptTest::updateOrCreate($where_rating, $insert_rating);

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Test answers saved successfully', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_image_to_category(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
            'image' => 'required',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            $searchCategory = Category::where(['id' => $request->category_id, 'user_id' => Auth::user()->id]);
            if ($searchCategory && $request->file('image')) {

                $attachment = $request->file('image');
                $storage_path = 'category/images/';
                $imgpath = commonUploadFile($storage_path, $attachment);

                $category = Category::find($request->category_id);
                $category->image_url = env('BASE_URL') . $imgpath;
                $category->save();

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Category image added successfully', $category);
            } else {
                return $this->apiResponse->sendResponse(401, 'Category Not Exits or Unauthorized User', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_keyword_to_video(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'keyword'   => 'required|string',
            'video_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            if (Auth::user()) {
                $keyword = Keyword::where('keyword', $request->keyword)->first();
                $video = Video::where('url', $request->video_url)->first();

                if (!$keyword) {
                    $keyword = new Keyword();
                    $keyword->keyword = $request->keyword;
                    $keyword->save();
                }
                if (!$video) {
                    $video = new Video();
                    $video->url = $request->video_url;
                    $video->save();
                }

                $keywordByUserExits = KeywordUser::where(['user_id' => Auth::user()->id, 'keyword_id' => $keyword->id])->first();
                $keywordOfVideoExits = KeywordVideo::where(['video_id' => $video->id, 'keyword_id' => $keyword->id])->first();

                if (!$keywordByUserExits && !$keywordOfVideoExits) {
                    Auth::user()->keywords()->attach($keyword->id);
                    $video->keywords()->attach($keyword->id);

                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Keyword added successfully', null);
                } elseif (!$keywordByUserExits) {
                    Auth::user()->keywords()->attach($keyword->id);

                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Keyword added successfully', null);
                } elseif (!$keywordOfVideoExits) {
                    $video->keywords()->attach($keyword->id);

                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Keyword added successfully', null);
                } else {

                    return $this->apiResponse->sendResponse(200, 'Already keyword exits by you', null);
                }
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_keyword_to_category(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id'  => 'required|integer',
            'keyword'      => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $keyword = Keyword::where('keyword', strtolower($request->keyword))->first();
            $category = Category::find($request->category_id);

            if (!$category) {
                DB::commit();
                return $this->apiResponse->sendResponse(404, 'Category Not Found', null);
            } else {
                if (!$keyword) {
                    $keyword = new Keyword();
                    $keyword->keyword = strtolower($request->keyword);
                    $keyword->save();
                }
            }

            $checkRecord = DB::table('category_keyword')->where(['user_id' => Auth::user()->id, 'category_id' => $category->id, 'keyword_id' => $keyword->id])->exists();

            if ($checkRecord) {
                DB::Commit();
                return $this->apiResponse->sendResponse(200, 'Already added this Category', null);
            }

            $keyword->categories()->attach($category->id, array('user_id' => Auth::user()->id));
            DB::Commit();
            return $this->apiResponse->sendResponse(200, 'Keyword added to this Category', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_keywords_of_category(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id'  => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $category = Category::find($request->category_id);

            if (!$category) {
                DB::commit();
                return $this->apiResponse->sendResponse(404, 'Category Not Found', null);
            }

            $keywords = $category->keywords()->groupBy('keyword')->get();
            DB::Commit();
            return $this->apiResponse->sendResponse(200, 'Keywords get successfully', $keywords);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function toggle_category_visibility(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $category = Category::find($request->category_id);

        $category->toggle_visibility();
        $category->save();

        return $this->apiResponse->sendResponse(200, 'Like Updated successfully', null);
    }

    public function category_user_id_change_to_admin(Request $request)
    {
        DB::beginTransaction();

        try {
//            if (Auth::user()->role_id == 3) {
                $change_user_id = Category::where('user_id', 0)->orWhere('user_id', null)->update(['user_id' => 1]);

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'User ID Changed Successfully', null);
//            } else {
//                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
//            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function video_annotataion_user_id_change_to_admin(Request $request)
    {
        DB::beginTransaction();
        try {
//            if (Auth::user()->role_id == 3) {
                $change_user_id = VideoAnnotation::where('user_id', 0)->update(['user_id' => 1]);

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'User ID Changed Successfully', null);
//            } else {
//                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
//            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_video_all_details(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'video_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            $searchVideo = Video::with('notes.user:id,name,avatar', 'ratings', 'keywords', 'learning_path.category.user:id,name,avatar')->where('url', $request->video_url)->first();
            if ($searchVideo) {
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Video with all details get successfully', $searchVideo);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(404, 'Video Not Found', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}
