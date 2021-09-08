<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Experience;
use App\Models\Listing_Image;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class LocationController extends Controller
{
    //
		function AddLocation (Request $req)
	{
		DB::beginTransaction();
		$location=new Location;
		$location->location_name=$req->location_name;
		$location->street_address1=$req->street_address1;
		$location->	street_address2=$req->street_address2;
		$location->	lat=$req->lat;
		$location->long=$req->long;
		$location->city=$req->city;
		$location->state=$req->state;
		$location->zip=$req->zip;
		$location->user_id=$req->user_id ;
		$result=$location->save();
		if($result)
		{
			DB::commit();
			return response()->json([
			'status' => true,
			'message'=> 'Location Added',
			'data'   => $location ]);

		}
		else
        {
			
			DB::rollBack();
			return response()->json([
			'status' => false,
			'message'=> 'location not Added']);
		}
	}
	function getLocation(Request $req)
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
				   $page = $req->PageNo;
				   $offset = ($page - 1) * 10;
				   $location = location::offset($offset)->limit(10)->get();
					return response()->json([
					'status' => true,
					'data' =>$location]);
		    }
	}

