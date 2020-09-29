<?php

namespace App\Http\Controllers;

use App\Opportunity;
use App\User;
use App\UserDetail;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataMigrationController extends Controller
{
   
    public function migrate_user_data () {
        $details = UserDetail::all();
        foreach($details as $detail){
            $user = User::where('id', $detail->user_id)->first();
            if(!is_null($detail->firstname))
                $user->firstname = $detail->firstname;
            if(!is_null($detail->lastname))
                $user->lastname = $detail->lastname;
            if(!is_null($detail->slug))
                $user->slug = $detail->slug;
            if(!is_null($detail->profile_link))
                $user->profile_link = $detail->profile_link;
            $user->save();
        }
    }

    public function migrate_eligible_regions_data(){
        $old_data = DB::table('eligible_region_opportunity')->get();
        foreach($old_data as $data){
            if(!is_null(Opportunity::find($data->opportunity_id))){
                DB::table('opportunity_eligibility')->insert(
                    array(
                        'opportunity_id' => $data->opportunity_id,
                        'country_id' => $data->eligible_region_id
                    )
                );
            }
        }
        return count($old_data);
    }

    public function migrate_opp_location_data(){
        $old_data = Opportunity::all(['id', 'opportunity_location_id']);
        foreach($old_data as $data){
                $id = $data->opportunity_location_id;
                if($data->opportunity_location_id == 243){
                    $id = 245;
                }
                DB::table('opportunity_location')->insert(
                    array(
                        'opportunity_id' => $data->id,
                        'country_id' => $id
                    )
                );
        }
        return count($old_data);
    }
}
