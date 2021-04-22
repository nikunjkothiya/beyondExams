<?php

namespace App\Http\Controllers;

use App\Search;
use App\SearchTermHistory;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SearchController extends Controller
{
    private $apiResponse;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function get_most_searched_terms(Request $request)
    {
        DB::beginTransaction();

        try {
            // $search =  $request->input('q');    // input serach box name parameter to access search value

            // if($search!=""){
            // $getsearches = Search::where(function ($query) use ($search){
            //     $query->where('search_term', 'like', '%'.$search.'%')
            //           ->orderBy('total_count', 'desc');
            // })->paginate(10);

            // $getsearches->appends(['q' => $search]);
            //     DB::commit();
            //     return $this->apiResponse->sendResponse(200, 'Successfully fetched search term.', $getsearches);
            // }else{
            $getsearches = Search::select('search_term', 'total_count')
                ->orderBy('total_count', 'desc')->paginate(10);   //10 records per call api
            if ($getsearches) {
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Successfully fetched search term.', $getsearches);
            } else {
                return $this->apiResponse->sendResponse(404, 'No data found.', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_search_term(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'search_term' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            $found = Search::where('search_term', $request->search_term)->first();
           // $authorization = $request->header('Authorization');

            if ($found) {
                $updateSearch = Search::find($found->id);
                $updateSearch->total_count += 1;
                $updateSearch->daily_count += 1;
                $updateSearch->save();

                $todayRecord = SearchTermHistory::where(['search_id' => $updateSearch->id])->whereDate('created_at', Carbon::today())->first();
                if ($todayRecord) {
                    $todayRecord->count += 1;
                    $todayRecord->save();
                } else {
                    SearchTermHistory::create(['search_id' => $updateSearch->id, 'count' => 1]);
                }

               // if ($authorization) {
                if (Auth::user()) {   
                    $exiting = $updateSearch->users()->where('user_id', Auth::user()->id)->exists();
                    if (!$exiting) {
                        $updateSearch->users()->attach(Auth::id());
                    }
                }
            } else {
                $newSearch = new Search();
                $newSearch->search_term = $request->search_term;
                $newSearch->total_count = 1;
                $newSearch->daily_count = 1;
                $newSearch->save();

                SearchTermHistory::create(['search_id' => $newSearch->id, 'count' => 1]);
               // if ($authorization)
               if (Auth::user())
                    $newSearch->users()->attach(Auth::id());
                // Auth::user()->id->searches()->attach($newSearch->id)
                // $updateSearch->users()->toggle(1, ['user_id' => 1]);
            }
            /* $search_term = Search::where('search_term', $request->search_term)->first();

            if ($search_term) {
                $search_term->total_count += 1;
                $search_term->daily_count += 1;
                $search_term->save();

                $todayRecord = DB::table('search_term_history')->where(['search_id' => $search_term->id])->whereDate('created_at', Carbon::today())->first();
                if ($todayRecord) {
                    $todayRecord->count += 1;
                    $todayRecord->save();
                } else {
                    DB::table('search_term_history')->insert(['search_id' => $search_term->id, 'count' => 1]);
                }
            } else {
                $search_term = new Search();
                $search_term->search_term = $request->search_term;
                $search_term->total_count = 1;
                $search_term->daily_count = 1;
                $search_term->save();
                DB::table('search_term_history')->insert(['search_id' => $search_term->id, 'count' => 1]);
            } */

            //                Auth::user()->searches()->attach([$search_term->id]);

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Search Term saved successfully.', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_daily_top_searched_results(Request $request)
    {
        DB::beginTransaction();

        try {
            $yesterday = date("Y-m-d", strtotime('-1 days') );
           
            $getsearches = SearchTermHistory::with('search_term:id,search_term')->whereDate('created_at', $yesterday)->orderBy('count','desc')->limit(10)->get();   //10 records per call api
            if ($getsearches) {
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Successfully fetched search term.', $getsearches);
            } else {
                return $this->apiResponse->sendResponse(404, 'No data found.', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }


    public function show(Search $search)
    {
        //
    }


    public function edit(Search $search)
    {
        //
    }


    public function update(Request $request, Search $search)
    {
        //
    }

    public function destroy(Search $search)
    {
        //
    }
}
