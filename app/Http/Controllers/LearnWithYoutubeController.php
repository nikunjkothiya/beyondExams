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

    public function addToSearchHistory()
    {
    }

    public function uniquelyIdentifyDevice(Request $request)
    {
    }

}
