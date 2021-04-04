<?php

namespace App\Http\Controllers;

use App;
use App\Country;
use App\Language;
use App\Tag;
use App\Video;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;
use stdClass;

class UtilController extends Controller
{
    protected $code = array();
    private $apiResponse;

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

    public function get_filters(Request $request)
    {
        try {
            $filters = Tag::all();

            return $this->apiResponse->sendResponse(200, 'All Tags fetched successfully', $filters);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }

    public function get_all_countries(Request $request)
    {
        $countries = Country::all();
        return $this->apiResponse->sendResponse(200, 'All countries fetched.', $countries);
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

  //  public function generate_all_sitemap()
  //  {
  //      $apiResponse = new ApiResponse;
  //      try {
  //          // Get sitemap index
  //          $lastOpp = Opportunity::latest('id')->first();
  //          $index = floor($lastOpp->id / 1000);
//
  //          $sitemapIndex = SitemapIndex::create();
  //          for ($i = 0; $i <= $index; $i++) {
  //              // Get Last 1000 Opportunity
  //              $opportunities = Opportunity::where('id', '>', ($i * 1000))->limit(1000)->get();
  //              // Start making sitemap
  //              $sitemap = Sitemap::create();
  //              // Loop through all opp
  //              foreach ($opportunities as $opportunity) {
  //                  resolve('url')->forceRootUrl('https://app.precisely.co.in/opportunity');
  //                  $sitemap->add(Url::create($opportunity->slug)->setPriority(0.5));
  //              }
  //              // Write to disk
  //              $sitemap_path = 'sitemaps/sitemap_' . ($i + 1) . '.xml';
  //              $sitemap->writeToDisk('public', $sitemap_path);
  //              resolve('url')->forceRootUrl('https://api.precisely.co.in/storage/');
  //              $sitemapIndex->add($sitemap_path);
  //          }
  //          $sitemapIndex->writeToDisk('public', 'sitemaps/sitemap_index.xml');
  //          resolve('url')->forceRootUrl(env('APP_URL'));
  //          return $apiResponse->sendResponse(200, "Success", $index);
  //      } catch (\Exception $e) {
  //          return $apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
  //      }
  //  }

  // public function generate_latest_sitemap()
  // {
  //     $apiResponse = new ApiResponse;
  //     try {
  //         // Get sitemap index
  //         $lastOpp = Opportunity::latest('id')->first(); //5000
  //         $index = floor($lastOpp->id / 1000);   // 5
  //         // Get Last 1000 Opportunity
  //         $opportunities = Opportunity::where('id', '>', ($index * 1000))->limit(1000)->get(); // >5000
  //         // Start making sitemap
  //         resolve('url')->forceRootUrl('https://app.precisely.co.in/opportunity');
  //         $sitemap = Sitemap::create();
  //         // Loop through all opp
  //         foreach ($opportunities as $opportunity) {
  //             $sitemap->add(Url::create($opportunity->slug)->setPriority(0.5));
  //         }
  //         // Write to disk
  //         $path = 'sitemaps/sitemap_' . ($index + 1) . '.xml';
  //         $sitemap->writeToDisk('public', $path);

  //         // Generate Sitemap Index
  //         resolve('url')->forceRootUrl('https://api.precisely.co.in/storage/');
  //         $sitemapIndex = SitemapIndex::create();
  //         for ($i = 0; $i <= $index; $i++) {
  //             $sitemap_path = 'sitemaps/sitemap_' . ($i + 1) . '.xml';
  //             $sitemapIndex->add($sitemap_path);
  //         }
  //         $sitemapIndex->writeToDisk('public', 'sitemaps/sitemap_index.xml');

  //         resolve('url')->forceRootUrl(env('APP_URL'));
  //         return $apiResponse->sendResponse(200, "Success", $index);
  //     } catch (\Exception $e) {
  //         return $apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
  //     }
  // }

    public function generate_all_sitemap()
    {
        $apiResponse = new ApiResponse;
        try {
            // Get sitemap index
            $lastVideo = Video::latest('id')->first();
            $index = floor($lastVideo->id / 1000);

            $sitemapIndex = SitemapIndex::create();
            for ($i = 0; $i <= $index; $i++) {
                // Get Last 1000 Videos
                $videos = Video::where('id', '>', ($i * 1000))->limit(1000)->get();
                
                // Start making sitemap
                $sitemap = Sitemap::create();
                // Loop through all videos
                foreach ($videos as $video) {
                    $title = youtube_data_api($video->url);

                    resolve('url')->forceRootUrl('https://beyondexams.org/dashboard/videos');
                        
                    $sitemap->add(Url::create('search?id='.$video->url.'&q='.$title)->setChangeFrequency('monthly')->setPriority(0.5)); 
                }
                // Write to disk
                $sitemap_path = 'sitemaps/sitemap_' . ($i + 1) . '.xml';
                $sitemap->writeToDisk('public', $sitemap_path);
                resolve('url')->forceRootUrl('https://api.learnwithyoutube.org/storage/');
                $sitemapIndex->add($sitemap_path);
            }
            $sitemapIndex->writeToDisk('public', 'sitemaps/sitemap_index.xml');
            resolve('url')->forceRootUrl(env('APP_URL'));
            return $apiResponse->sendResponse(200, "Successfully Sitemap Generated", $index);
        } catch (\Exception $e) {
            return $apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

}
