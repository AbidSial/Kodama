<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Experience;
use App\Models\User;
use App\Models\Profile;
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
			//'user_type'=>'required',
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
                $role = "Customer";
				
				if($req->has('role')){
					$role = $req->role;
				}
				$term = '';
				
				$users=DB::table('users')
					->join('profiles', function ($join) {
					$join->on('users.id', '=', 'profiles.user_id');
						
				})->where('users.role', $role)
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
				->where('users.role', $role)
				->select('users.id, users.email, users.phone', 
				'profiles.full_name, profiles.street_address, profiles.business_name, profiles.image_url, profiles.reservation_website, profiles.profile_bio');
				})
				->select('users.id', 'users.email'
						, 'profiles.full_name', 'profiles.image_url')
				->skip($off_set)->take($Rows_To_Fetch)->get();
			}
			
					 
			return response()->json([
				'status' => true,
				'message' =>' User Searched',
				'data' => $users,
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
				
			 $experiences = experience::where('id', $req->id)->get();
						
				    foreach($experiences as $ex) {
						$exid = $ex->id;
						$images = Listing_Image::where('listing_id', $exid)->get();
						$ex["images"] = $images;
					}
					
				      return response()->json([
					       'status' => true,
						   'message' => 'Featured don',
					       'data' =>$experiences]);
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
					 $experiences=Experience::where('id',$req->id)->get();
					 foreach($experiences as $aex) 
					   {
						$aexid = $aex->id;
						$images = Listing_Image::where('listing_id', $aexid)->get();
						$aex["images"] = $images;
					   }
					
				          return response()->json([
					         'status' => true,
							 'message'=> 'Obtained Experience Detail',
					         'data' =>$experiences]);
			}
					
					
		}		

