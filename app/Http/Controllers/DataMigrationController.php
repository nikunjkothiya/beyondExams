<?php

namespace App\Http\Controllers;

use App\Opportunity;
use App\User;
use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataMigrationController extends Controller
{
    
    public function migrate_user_details_to_student_details(){
        $old_data = DB::table('user_details')->get();
        foreach($old_data as $data){
            $role = UserRole::where('user_id', $data->user_id)->first();

            if(is_null($role) ||  $role->is_user === 1){
                DB::table('student_details')->insert(
                    array(
                        'user_id' => $data->user_id,
                        'college' => $data->college,
                        'city' => $data->city,
                        'gpa' => $data->gpa,
                        'country_id' => $data->country_id,
                        'discipline_id' => $data->discipline_id,
                        'qualification_id' => $data->qualification_id,
                        'created_at' => $data->created_at,
                        'updated_at' => $data->updated_at,
                    ),
                );
            }
        }
        return count($old_data);
    }

    public function migrate_mentor_details_to_user_details(){
        $old_data = DB::table('mentor_details')->get();
        foreach($old_data as $data){
            $role = UserRole::where('user_id', $data->user_id)->first();

            if(is_null($role) || $role->is_mentor === 1){
                DB::table('user_details')->insert(
                    array(
                        'user_id' => $data->user_id,
                        'firstname' => $data->firstname,
                        'lastname' => $data->lastname,
                        'email' => $data->email,
                        'profile_link' => $data->profile_link,
                        'slug' => $data->slug,
                        'created_at' => $data->created_at,
                        'updated_at' => $data->updated_at,
                    ),
                );
            }
        }
        return count($old_data);
    }

    public function migrate_eligible_regions_data(){
        $old_data = DB::table('eligible_region_opportunity')->get();
        foreach($old_data as $data){
            if(!is_null(Opportunity::find($data->opportunity_id))){
                DB::table('opportunity_eligibility')->insert(
                    array(
                        'opportunity_id' => $data->opportunity_id,
                        'country_id' => $data->eligible_region_id
                    ),
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
                    ),
                );
        }
        return count($old_data);
    }
}
