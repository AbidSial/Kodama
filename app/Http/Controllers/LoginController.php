<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    //
	function Login(Request $req)
	{
		$credentials = $req->only ('email','password');
		try
		{
			if(! $token = JWTAuth::attempt($credentials))
			{
				return response()->json([
				'message' =>'Invalid_Credentials',
				'status' =>false,
				'data' => null,
				]);
			}
		}
		Catch (JWTException $e)
		{
			return response()->json([
			'message' => 'Could not create token',
			'status'=>false,
			'data' => null,]);
		}
		 $user= User::where('email', $req->email)->first();
		 $id=$user->id;
		 $profile=Profile::where('user_id',$id)->first();
		 $profile["email"] = $user->email;
		 $profile["phone"] = $user->phone;
		 $profile["role"] = $user->role;
		 $profile["id"] = $user->id;
		 $data = ["profile" => $profile, "access_token" => compact('token')];
				return response()->json([
				'message'=> 'User Logged in',
				'status' =>  true,
				'data'   => $data
				]);
	}
}
	
