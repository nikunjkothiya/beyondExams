<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;
use App\Country;
use App\Language;
use App\Tag;
use Auth;
use Carbon\Carbon;
use DateTime;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App as FacadesApp;
use Illuminate\Support\Facades\Mail;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;
use stdClass;


class Video extends Model
{
    protected $fillable = [
        'url'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            // ... code here
        });

        self::created(function ($model) {
            $response = youtube_data_api($model->url);

            if ($response == 0) {
                $myfile = fopen(public_path() . "/dummyVideoUrl.txt", "a") or die("Unable to open file!");
                $txt = $model->url . "\n";
                fwrite($myfile, $txt);
                fclose($myfile);
                return true;
            }

            Video::where('id', $model->id)->update(['slug' => $response['slug'], 'title' => $response['title'], 'description' => $response['description']]);

            $url = 'https://beyondexams.org/dashboard/videos/search?id=' . $model->url . '&q=' . $response['slug'];
            $date = date('c', strtotime($model->updated_at));

            $index = floor($model->id / 1000);
            $path = storage_path('app/public/sitemaps/sitemap_' . ($index + 1) . '.xml');
            $isExists = file_exists($path);
            if (!$isExists) {

                $xmlString = '<?xml version="1.0" encoding="UTF-8"?>
            <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
            </urlset>';

                $dom = new DOMDocument;
                $dom->preserveWhiteSpace = FALSE;
                $dom->loadXML($xmlString);
                //Save XML as a file
                $dom->save($path);
            }


            $objDOM = new DOMDocument();
            $objDOM->preserveWhiteSpace = false;
            $objDOM->formatOutput = true;
            $objDOM->load($path, LIBXML_NOWARNING);
            $urlset = $objDOM->getElementsByTagName("urlset")->item(0);

            $newAdd = $objDOM->createElement("url");
            $locAdd = $objDOM->createElement("loc", htmlentities($url, ENT_XML1));
            $lastmodAdd = $objDOM->createElement("lastmod", $date);
            $changefreqAdd = $objDOM->createElement("changefreq", "monthly");
            $priorityAdd = $objDOM->createElement("priority", "0.5");

            $newAdd->appendChild($locAdd);
            $newAdd->appendChild($lastmodAdd);
            $newAdd->appendChild($changefreqAdd);
            $newAdd->appendChild($priorityAdd);

            $urlset->appendChild($newAdd);
            $objDOM->save($path);
        });

        self::updating(function ($model) {
            // ... code here
        });

        self::updated(function ($model) {
            // ... code here
        });

        self::deleting(function ($model) {
            // ... code here
        });

        self::deleted(function ($model) {
            // ... code here
        });
    }


    public function num_likes()
    {
        return $this->likes()->count();
    }

    public function likes()
    {
        return $this->belongsToMany('App\User', 'user_video')->where('type', 'liked');
    }

    public function ses()
    {
        return $this->hasOne('App\Ses');
    }

    public function users()
    {
        return $this->belongsToMany('App\User')->wherePivot('type', 'history')->withTimestamps();
    }

    public function bookmarkByUser()
    {
        return $this->belongsToMany('App\User', 'bookmark_video')->withTimestamps();
    }

    public function duration_history()
    {
        return $this->hasMany('App\HistoryUserVidoes', 'video_id', 'id');
    }

    public function keywords()
    {
        return $this->belongsToMany('App\Keyword')->withTimestamps();
    }

    public function notes()
    {
        return $this->hasMany('App\Note', 'resource_url', 'url');
    }

    public function ratings()
    {
        return $this->hasMany('App\VideoRating', 'video_id', 'id');
    }

    public function learning_path()
    {
        return $this->hasOne('App\LearningPath', 'video_id', 'id');
    }

    public function annotations()
    {
        return $this->hasMany('App\VideoAnnotation', 'video_id', 'id');
    }
}
