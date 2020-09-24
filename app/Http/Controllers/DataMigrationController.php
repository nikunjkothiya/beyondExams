<?php

namespace App\Http\Controllers;

use App\Opportunity;
use App\User;
use App\UserDetail;
use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataMigrationController extends Controller
{

    public function migrate_user_info(){
        $users = DB::table('users')->get();
        foreach($users as $u){
            $user = User::where('id', $u->id)->first();
            $detail_table = UserDetail::where('user_id', $user->id)->first();
            if($detail_table){

                // Check if name is null in users table but is avaiable in user_details
                if(is_null($user->name) && !is_null($detail_table->firstname)){
                    $user->name = $detail_table->firstname . ' ' . $detail_table->lastname;
                }

                // Check if name is avaiable in users table but is null in user_details
                if(!is_null($user->name) && is_null($detail_table->firstname)){
                    $break_name = explode(" ",$user->name, 2);
                    $detail_table->firstname = $break_name[0];
                    if(count($break_name) === 2){
                        $detail_table->lastname = $break_name[1];
                    } else {
                        $detail_table->lastname = null;
                    }
                }

                // Check if email is null in users table but is avaiable in user_details
                if(is_null($user->email) && !is_null($detail_table->email)){
                    $user->email = $detail_table->email;
                }

                // Check if email is avaiable in users table but is null in user_details
                if(!is_null($user->email) && is_null($detail_table->email)){
                    $detail_table->email = $user->email;
                }

                // Save both details
                $user->save();
                $detail_table->avatar = $user->avatar;
                $detail_table->save();

            } else {
                // if(!is_null($user->name) || !is_null($user->email)){
                    $new = new UserDetail();
                    $new->user_id = $user->id;
                    $new->email = $user->email;
                    $new->phone = $user->phone;
                    $new->avatar = $user->avatar;
    
                    $break_name = explode(" ",$user->name, 2);
                    $new->firstname = $break_name[0];
                    if(count($break_name) === 2){
                        $new->lastname = $break_name[1];
                    } else {
                        $new->lastname = null;
                    }
    
                    if(count($break_name) === 1){
                        $slug = str_replace(" ", "-", strtolower($break_name[0])) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 16);
                        $new->slug = $slug;
                    } elseif (count($break_name) === 2){
                        $slug = str_replace(" ", "-", strtolower($break_name[0] . $break_name[1])) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 16);
                        $new->slug = $slug;
                    }
                    
                    $new->save();
                // }
            }
        }

        return count($users);
    }

    public function migrate_user_details(){
        // user_details ie student unique data to student_details
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

        // mentor_details ie mentor common data to user_details
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
