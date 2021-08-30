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
			return response()->json($validator->errors());
				}
				
				DB::beginTransaction();
		$user=new User;
		$user->phone=$req->phone;
		$user->email=$req->email;
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
			$data = ['user'=> $response, 'access_token'=> compact('token')];
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
			return response()->json($validator->errors());
			  }
				
				$profile=new profile;
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
			return response()->json($validator->errors());
				}
		{
			$isEmailAvailable = $this->isEmailAvailable($req['email']);
			if($isEmailAvailable)
			{
				return response()->json([
				'status' => 'true',
				'message'=> 'Email_Available']);
			}
			else
			{
				return response()->json([
				'status' => 'false',
				'message'=> 'Email_not_Available']);
			}
		}
}
  	public function CheckPhoneAvailablity(Request $req)
	{
				$validator = Validator::make($req->all(), [
				'phone' => 'required|max:12',
					]);
			if($validator->fails()){
			return response()->json($validator->errors());
				}
		{
			if($this->isPhoneAvailable($req['phone']))
			{
			return response()->json([
			'status' => 'true',
			'message'=> 'Phone_Available']);
			}
			else
			{
				return response()->json([
				'status' => 'false',
				'message'=> 'Phone_not_Available']);
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
		 
		function ViewUser(Request $req)
            {
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
							'message' => 'token is absent',
						]);      

                    }
					return response()->json([
					'status' => true,
					'data' =>$user]);
		    }
}

			
			
			


