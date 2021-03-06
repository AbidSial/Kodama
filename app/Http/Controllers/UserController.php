<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Experience;
use App\Models\User;
use App\Models\Profile;
use App\Models\Location;
use App\Models\Listing_Image;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    //
	
		 
		function searchUser(Request $req)
            {
						
			$validator = Validator::make($req->all(), [
			'off_set' => 'required',
				]);
			if($validator->fails()){
			return response()->json(['status' => false,
			'message'=> 'validation error',
			'data' => null, 
			'validation_errors'=> $validator->errors()]);
				}
				
				$user = Auth::user();
				$email = $user->email;
				if($email != 'admin@kodamaapp.com'){
					return response()->json([
						   'status' => false,
						   'message' => 'Only admin is authorized to perform this action',
						   'data' => null,
						]);
				}
				$Rows_To_Fetch = 10;
				$off_set = $req->off_set;
				$off_set = $off_set * $Rows_To_Fetch;
                
					 $term = '';
					 $users=DB::table('users')
						->join('profiles', function ($join) {
						$join->on('users.id', '=', 'profiles.user_id');
						
					})
					->select('users.id', 'users.email'
						, 'profiles.full_name', 'profiles.image_url')
					->skip($off_set)->take($Rows_To_Fetch)
					->get();
			if($req->has('term')){
				$term = $req->term;
				$users=DB::table('users')
				->join('profiles', function ($join) use($term) {
				$join->on('users.id', '=', 'profiles.user_id')
				->where('profiles.full_name', 'like', "%$term%")
				->select('users.id, users.email, users.phone', 
				'profiles.full_name, profiles.street_address, profiles.business_name, profiles.image_url, profiles.reservation_website, profiles.profile_bio');
				})
				->select('users.id', 'users.email'
						, 'profiles.full_name', 'profiles.image_url')
				->skip($off_set)->take($Rows_To_Fetch)->get();
			}
					 //$users=User::skip($off_set)->take($Rows_To_Fetch)->get();
					 
					 
					 
		    
                    return response()->json([
						   'status' => true,
						   'data' => $users,
						   'message' => 'Users obtained'
						]);
					 
	}
	
	function makeFeatured(Request $req)
            {
			   $validator = Validator::make($req->all(), [
			   'id' => 'required',
				]);
			if($validator->fails()){
			  return response()->json(['status' => false,
			        'message'=> 'validation error',
			        'data' => null, 
			        'validation_errors'=> $validator->errors()]);
				}
				
                $user = Auth::user();
				$email = $user->email;
				if ($email != 'admin@kodamaapp.com'){
					return response()->json(['status' => false,
			        'message'=> 'Only admin can perform this action',
			        'data' => null,]);
				 }
				
			Experience::where('id', $req->id)->update(['isFeature'=>true]);
			$experience = experience::where('id', $req->id)->first();
			$exid = $experience->id;
			$images = Listing_Image::where('listing_id', $exid)->get();
			$experience["images"] = $images;
					
					
				      return response()->json([
					       'status' => true,
					       'data' =>$experience,
					       'message' => 'Experience featured',
					       ]);
					}
        	
		public function getExperienceDetail(Request $req)
		{
			  $validator = Validator::make($req->all(), [
			  'id' => 'required',
				]);
			  if($validator->fails())
			    {
			     return response()->json(['status' => false,
			        'message'=> 'validation error',
			        'data' => null, 
			        'validation_errors'=> $validator->errors()]);
				}
				$user = Auth::user();
				
				if($user == null)
					{
					  return response()->json(['status'=>false,
					     'message'=>"You're not authorized to perform this action",
						 'data'=>'null'
					      ]);
					}
					//get experience images
					$experience=Experience::where('id',$req->id)->first();
					$aexid = $experience->id;
				 	$images = Listing_Image::where('listing_id', $aexid)->get();
					$experience["images"] = $images;
					
					//get owner detail
					$uid = $experience["user_id"];
					$profile = DB::table('profiles')->where('user_id', $uid)
					->select('full_name', 'image_url', 'user_id')
					->first();
					$profile->id = $uid;
					$experience["profile"] = $profile;
					
					//get location of experience
					$locid = $experience["location_id"];
					$loc = $this->getLocation($locid);
					$experience["location"] = $loc;
					
					// get 3 listings added by the same owner
					$experiencesByOwner = $this->getExperiencesForUser($uid, 3);
					
					
				          return response()->json([
					         'status' => true,
					         'data' =>["experience" =>$experience, "morelistings"=> $experiencesByOwner],
					         'message' => 'Experience list obtained'
					         ]);
			}
			
			private function getExperiencesForUser($uid, $count){
			    $experiences = Experience::where('user_id', $uid)->take(3)->get();
			    foreach($experiences as $ex){
			        $exid = $ex->id;
				 	$images = Listing_Image::where('listing_id', $exid)->get();
					$ex["images"] = $images;
					$loc = $this->getLiteLocation($ex->location_id);
					$ex["location"] = $loc;
			    }
			    return $experiences;
			}
			
			public static function getLocation($locid){
					$loc = Location::where('location_id', $locid)->first();
					return $loc;
			}
			
			public static function getLiteLocation($locid){
					$loc = Location::where('location_id', $locid)->first();
					return ["city"=>$loc->city, "state"=> $loc->state, "location_id" => $loc->location_id];
			}
					
					
		}		

