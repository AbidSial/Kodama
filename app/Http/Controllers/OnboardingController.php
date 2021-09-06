<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\profile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class OnboardingController extends Controller
{
    //
	function Register(Request $req)
	{
			{	
			$validator = Validator::make($req->all(), [
			'email' => 'required|string|email|max:255|unique:users',
			'phone' => 'required|unique:users',
			'password' => 'required|string|max:40',
				]);
			if($validator->fails()){
			return response()->json(['status' => false,
			'message'=> 'validation error',
			'data' => null, 
			'validation_errors'=> $validator->errors()]);
				}
				
				DB::beginTransaction();
		$user=new User;
		$user->phone=$req->phone;
		$user->email=$req->email;
		$user->role=$req->role;
		$user->password=Hash::make($req->password);
		$result=$user->save();
		$token = JWTAuth::fromUser($user);
		
		$user_id = $user->id;
		if($result)
		    {
				$response = $this->AddProfile($req, $user_id);
			if($response == null){
				DB::rollBack();
				return response()->json([
					'message' => 'User not registered',
					'status' => false,
					'data' => null,
				]);
			}
			DB::commit();
			$response["role"] = $user->role;
			$response["phone"] = $user->phone;
			$data = ['profile'=> $response, 'access_token'=> compact('token')];
			return response()->json([
					'message' => 'User registered',
					'status' => true,
					'data' =>$data,
			]);
		    }
		else
			{
				return response()->json([
					'message' => 'User registered',
					'status' => false,
					'data'=> null
					
			]);
			}
		}  
		}
		
		function AddProfile(Request $req, $user_id)
		{
			{	
			$validator = Validator::make($req->all(), [
			'full_name' => 'required|string|max:50',
				]);
			if($validator->fails())
			  {
				return response()->json(['status' => false,
				'message'=> 'validation error',
				'data' => null, 
				'validation_errors'=> $validator->errors()]);
			  }
				
				$profile=new Profile;
				if($req->hasFile('profile_image'))
				{
					$data=$req->file('profile_image')->store('Images');
					$profile->image_url = $data;
				}
				else
				{
					return null;
				}
		
		$profile->full_name=$req->full_name;
		$profile->business_name=$req->business_name;
		$profile->reservation_website=$req->reservation_website;
		$profile->street_address=$req->street_address;
		$profile->profile_bio=$req->profile_bio;
		$profile->user_id = $user_id;
		$result=$profile->save();
		if($result)
		    {
				
			return $profile;
			
		    }
		else
			{
				return null;
			}
		}  
	}
	public function CheckEmailAvailablity(Request $req)
	{
				$validator = Validator::make($req->all(), [
				'email' => 'required|string|email|max:255',
					]);
			if($validator->fails()){
					return response()->json(['status' => false,
					'message'=> 'validation error',
					'data' => null,
					'validation_errors'=> $validator->errors()]);
				}
		{
			$isEmailAvailable = $this->isEmailAvailable($req['email']);
			if($isEmailAvailable)
			{
				return response()->json([
				'status' => true,
				'message'=> 'Email Available'
				'data' =>null, ]);
			}
			else
			{
				return response()->json([
				'status' => false,
				'message'=> 'Email not Available'
				'data' => null,]);
			}
		}
}
  	public function CheckPhoneAvailablity(Request $req)
	{
				$validator = Validator::make($req->all(), [
				'phone' => 'required|max:12',
					]);
			if($validator->fails()){
					return response()->json(['status' => false, 
					'message'=> 'validation error', 
					'data' => null, 
					'validation_errors'=> $validator->errors()]);
				}
		{
			if($this->isPhoneAvailable($req['phone']))
			{
			return response()->json([
			'status' => true,
			'message'=> 'Phone Available'
			'data' => null,]);
			}
			else
			{
				return response()->json([
				'status' => false,
				'message'=> 'Phone not Available'
				'data' => null,]);
			}
		}
}	  

	private function isEmailAvailable($email)
			{
				$user=User::where("email",$email)->first();
				if($user==null)
				{
					return true;
				}
				else
				{
					return false;
				}
				
			}
	private function isPhoneAvailable($phone)
			{
				$user=User::where("phone",$phone)->first();
				if($user==null)
				{
					return true;
				}
				else
				{
					return false;
				}
				
			}
		 
		function GetUserDetail(Request $req)
            {
				$validator= Validator::make($req->all(),[
				'off_set' => 'required']);
				if($validator->fails())
				{
				 return response()->json([
				  'status' => false,
				  'message'=> 'validator_error',
				  'data'   => null,
				  'Validation_error' => $validator->errors()
			       ]);
				}
				$Row_To_Fetch = 10;
				$off_set= $req->off_set;
				$off_set=$off_set * $Row_To_Fetch;
                try 
				   {

                    if (! $user = JWTAuth::parseToken()->authenticate())
							{
                          return response()->json([
							'status' => false,
							'message' => 'user is not found',
							'data' => null,
                           ]);							
                            }
				   } 
					catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e)
					{
					return response()->json([
							'status' => false,
							'message' => 'token is expired',
							'data' => null,
                          ]); 

                    }
					catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e)
					{
						return response()->json([
							'status' => false,
							'message' => 'token is invalid',
							'data' => null,
						]);
                    } 
					catch (Tymon\JWTAuth\Exceptions\JWTException $e)
					 {

                    return response()->json([
						   'status' => false,
						   'message' => 'token is absent',
						   'data'=> null,
						]);      

                     }
				$users = User::skip($off_set)->take($Row_To_Fetch)->get();
						
				    foreach($users as $ur) {
						$userid = $ur->id;
						$profile = Profile::where('user_id', $userid)->get();
						$ur["profile"] = $profile;
					}
					
				      return response()->json([
					       'status' => true,
						   'message' =>'Obtained User Detail',
					       'data' =>$users]);
					}
}
			
			
			


