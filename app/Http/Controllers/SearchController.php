<?php

namespace App\Http\Controllers;

use App\Search;
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
                $search_term = Search::where('search_term', $request->search_term)->first();

                if ($search_term) {
                    $search_term->total_count += 1;
                    $search_term->daily_count += 1;
                    $search_term->save();
                } else {
                    $search_term = new Search();
                    $search_term->search_term = $request->search_term;
                    $search_term->total_count = 1;
                    $search_term->daily_count = 1;
                    $search_term->save();
                }

                Auth::user()->searches()->attach([$search_term->id]);

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
