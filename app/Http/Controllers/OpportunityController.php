<?php

namespace App\Http\Controllers;

use App\Language;
use App\Opportunity;
use App\PlusTransaction;
use App\User;
use Auth;

class OpportunityController extends Controller
{
    protected $languages;

    public function __construct()
    {
        $this->languages = Language::all();
        $this->txnflag = new SubscriptionController;
    }

    public function get_opp($slug)
    {
        try {
            $opportunity = Opportunity::where('slug', $slug)->firstOrFail();
            $flag = 0;
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                $flag = $this->txnflag->check_subscription(Auth::user()->id);
                $plus_status = PlusTransaction::where('user_id', $user->id)->where('opportunity_id', $opportunity->id)->select('status');
            };
        } catch (Exception $e) {
            abort(404);
        }
        return view('pages.opportunity', ['opportunity' => $opportunity, 'languages' => $this->languages, 'txnflag' => $flag, 'plus_status' => $plus_status]);
    }
}
