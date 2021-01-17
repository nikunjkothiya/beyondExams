<?php

namespace App\Http\Controllers;

use DB;
use App\Search;
use App\Search_User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    private $apiResponse;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function get_most_searched_terms()
    {
        DB::beginTransaction();

        try {

            $getsearches = Search::select('search_term', 'total_count')
                ->orderBy('total_count', 'desc')->paginate(15);   //15 records per call api
            if ($getsearches) {
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Successfully fetched search term.', $getsearches);
            } else {
                return $this->apiResponse->sendResponse(200, 'No data found.', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function add_search_term(Request $request)
    {
        DB::beginTransaction();
        if (Auth::check()) {
            $validator = Validator::make($request->all(), [
                'search_term' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
            }

            try {

                $last = Search::where('search_term', $request->search_term)->first();
                $newSearch = new Search();
                if ($last) {
                    $newSearch->total_count = $last->total_count + 1;
                    $newSearch->daily_count = $last->daily_count + 1;
                } else {
                    $newSearch->search_term = $request->search_term;
                    $newSearch->total_count = 1;
                    $newSearch->daily_count = 1;
                }
                $newSearch->save();

                $latestid = $newSearch->id;
                $newsearchuser = new Search_User();
                $newsearchuser->search_id = $latestid;
                $newsearchuser->user_id = Auth::user()->id;
                $newsearchuser->save();

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Search Term saved successfully.', null);
            } catch (\Exception $e) {
                DB::rollback();
                throw new HttpException(500, $e->getMessage());
            }
        } else {
            return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
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
