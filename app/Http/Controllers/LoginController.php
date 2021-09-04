<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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
				'status' =>false]);
			}
		}
		Catch (JWTException $e)
		{
			return response()->json([
			'message' => 'Could not create token',
			'status'=>false]);
		}
		 $data= User::where('email', $req->email)->first();
				return response()->json([
				'message'=> 'User Login',
				'status' =>  true,
				'data'   => $data,
				'access_token' => compact('token')
				]);
	}
}
	
