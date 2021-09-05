<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Experience;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
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
				$Rows_To_Fetch = 10;
				$off_set = $req->off_set;
				$off_set = $off_set * $Rows_To_Fetch;
                try 
				   {

                    if (! $user = JWTAuth::parseToken()->authenticate())
							{
                          return response()->json([
							'status' => false,
							'message' => 'user is not found',
                           ]);							
                            }
				   } 
					catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e)
					{
					return response()->json([
							'status' => false,
							'message' => 'token is expired',
                          ]); 

                    }
					catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e)
					{
						return response()->json([
							'status' => false,
							'message' => 'token is invalid',
						]);
                    } 
					catch (Tymon\JWTAuth\Exceptions\JWTException $e)
					 {
						return response()->json([
							'status' => false,
							'message' => 'token is invalid',
						]);

                     }
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
				->where('profiles.full_name', 'like', '%$term%')
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
						]);
					 
	}
}

