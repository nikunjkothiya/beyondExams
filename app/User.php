<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'unique_id', 'user_role', 'phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role(){
        return $this->belongsToMany('App\Role', 'role_user');
    }

    public function session(){/////
        return $this->hasMany('App\Session');
    }

    public function chats(){
        return $this->belongsToMany('App\Chat');
    }

    public function mentor_verification(){
        return $this->hasOne('App\MentorVerification');
    }

    public function tags(){
        return $this->belongsToMany('App\Tag');
    }

   /*  public function domains(){/////
        return $this->belongsToMany('App\DomainUser');
    } */

    public function saved_opportunities(){/////
        return $this->belongsToMany('App\Opportunity');
    }

    public function viewed_opportunity(){/////
        return $this->belongsToMany('App\UserViewedOpportunity');
    }

    public function premium_subscription(){/////
        return $this->hasOne('App\Transaction');
    }
    public function plus_subscription(){/////
        return $this->hasMany('App\PlusTransaction');
    }
    public function premium_txn(){/////
        return $this->hasOne('App\PremiumTxn');
    }

    public function details(){/////
        return $this->hasOne('App\UserDetail');
    }

    public function social_accounts(){
        return $this->hasMany('App\UserSocial');
    }

    public function followers(){
        return $this->hasMany('App\UserFollower', 'user_id');
    }

    public function influencers(){
        return $this->hasMany('App\UserFollower', 'influencer_id');
    }

    public function opportunities(){/////
        return $this->belongsToMany('App\Opportunity');
    }

    public function validateForPassportPasswordGrant($password)/////
    {
        //$owerridedPassword = 'password';
        //return Hash::check($password, $this->password);
        return true;
    }

    public function findForPassport($username)
    {
        return $this->where('unique_id', $username)->first();
    }

    public function analytics(){/////
        return $this->hasMany('App\Analytics');
    }

    public function user_key(){/////
        return $this->hasMany('App\UserKey');
    }

    public function user_resource(){/////
        return $this->hasMany('App\UserResource');
    }

    public function student_firebase_id(){/////
        return $this->hasMany('App\StudentFirebase');
    }

    public function admin_firebase_id(){//////
        return $this->hasMany('App\AdminFirebase');
    }

    public function is_admin(){
        return $this->role()->is_admin;
    }

    public function comments(){
        return $this->hasMany('App\Comment');
    }

    public function test_scores(){
        return $this->hasMany('App\TestScore');
    }

    public function videos(){
        return $this->belongsToMany('App\Video', 'user_video')->withPivot('type')->withTimestamps();
    }

//    public function history(){
//        return $this->videos()->where('type', 'history');
//    }

    public function history(){
//<<<<<<< HEAD
//        return $this->belongsToMany('App\Video', 'watched_history')->withTimestamps();
	return $this->videos()->wherePivot('type', 'history')->orderByDesc('updated_at');
//=======
//        return $this->belongsToMany('App\Video')->wherePivot('type', 'history')->withTimestamps();
//	return $this->videos()->wherePivot('type', 'history')->withTimestamps();
//>>>>>>> 9fb88ecc34adf5cbd520f34430d64fe6592fd603
    }

//    public function liked_videos(){
//        return $this->videos()->wherePivot('type', 'liked');
//    }

    public function liked_videos(){
        return $this->videos()->wherePivot('type', 'liked');
    }

    public function searches(){
        return $this->belongsToMany('App\Search', 'search_user');
    }

    public function watchHistoryVidoes(){
        return $this->belongsToMany('App\Video','history_user_videos')->withTimestamps();
    }

    public function getAllHistoryVideos(){
        return $this->belongsToMany('App\HistoryUserVidoes')->withTimestamps();
    }

    public function giveVideoRating(){
        return $this->hasOne('App\VideoRating','user_id','id');
    }

    public function bookmarkVideo(){
        return $this->belongsToMany('App\Video','bookmark_video')->withTimestamps();
    }

    public function keywords() {
        return $this->belongsToMany('App\Keyword')->withTimestamps();
    }

    public function certificates(){
        return $this->hasMany('App\UserCertificate','user_id','id');
    }

    public function chat_reviews(){
        return $this->hasMany('App\ChatReview','student_id','id');
    }

    public function time_tables(){
        return $this->hasMany('App\TimeTable','teacher_id','id');
    }

    public function domains() {
        return $this->belongsToMany('App\Domain','domain_user')->withTimestamps();
    }

}
