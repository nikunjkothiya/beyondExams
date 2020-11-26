<?php

namespace App\Http\Controllers;

use App;
use App\Category;
use App\EligibleRegion;
use App\FundType;
use App\Language;
use App\Opportunity;
use App\OpportunityLocation;
use App\PlusTransaction;
use App\Tag;
use App\User;
use App\UserDetail;
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
    private $apiResponse;
    protected $code = array();

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
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

    public function addNewCategory(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'level' => 'required|integer',
            'previous_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        if (Auth::user()->role() == 1)
            return $this->apiResponse->sendResponse(401, 'User unauthorised.', null);

        $category = Category::create(['title'=>$request->title, 'level'=>$request->level, 'previous_id'=>$request->previous_id]);

        return $this->apiResponse->sendResponse(200, 'New Category added', $category);
    }

    public function getCategories(Request $request) {
        $validator = Validator::make($request->all(), [
            'level' => 'required|integer',
            'previous_id' => 'integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        if ($request->level > 1) {
            if (!$request->previous_id) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing.', 'previous_id parameter not included');
            }
            $previous_id = $request->previous_id;
        } else {
            $previous_id = 0;
        }

        $categories = Category::where('level', $request->level)->where('previous_id', $previous_id)->get();

        return $this->apiResponse->sendResponse(200, 'Categories fetched successfully', $categories);

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

    public function generate_all_sitemap()
    {
        $apiResponse = new ApiResponse;
        try {
            // Get sitemap index
            $lastOpp = Opportunity::latest('id')->first();
            $index = floor($lastOpp->id / 1000);

            $sitemapIndex = SitemapIndex::create();
            for ($i = 0; $i <= $index; $i++) {
                // Get Last 1000 Opportunity
                $opportunities = Opportunity::where('id', '>', ($i * 1000))->limit(1000)->get();
                // Start making sitemap
                $sitemap =  Sitemap::create();
                // Loop through all opp
                foreach ($opportunities as $opportunity) {
                    resolve('url')->forceRootUrl('https://app.precisely.co.in/opportunity');
                    $sitemap->add(Url::create($opportunity->slug)->setPriority(0.5));
                }
                // Write to disk
                $sitemap_path = 'sitemaps/sitemap_' . ($i + 1) . '.xml';
                $sitemap->writeToDisk('public', $sitemap_path);
                resolve('url')->forceRootUrl('https://api.precisely.co.in/storage/');
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
            $opportunities = Opportunity::where('id', '>', ($index * 1000))->limit(1000)->get();
            // Start making sitemap
            resolve('url')->forceRootUrl('https://app.precisely.co.in/opportunity');
            $sitemap =  Sitemap::create();
            // Loop through all opp
            foreach ($opportunities as $opportunity) {
                $sitemap->add(Url::create($opportunity->slug)->setPriority(0.5));
            }
            // Write to disk
            $path = 'sitemaps/sitemap_' . ($index + 1) . '.xml';
            $sitemap->writeToDisk('public', $path);

            // Generate Sitemap Index
            resolve('url')->forceRootUrl('https://api.precisely.co.in/storage/');
            $sitemapIndex = SitemapIndex::create();
            for ($i = 0; $i <= $index; $i++) {
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
}
