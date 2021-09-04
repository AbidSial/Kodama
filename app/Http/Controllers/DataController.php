<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Experience;
use App\Models\Listing_Image;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class DataController extends Controller
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
   function AddExperience(Request $req)
   {     DB::beginTransaction();
	   $experience = new Experience;
       $experience->item_title=$req->item_title;
	   $experience->item_description=$req->item_description;
	   $experience->reservation_website=$req->reservation_website;
	   $experience->location_id=$req->location_id;
	   $experience->item_price=$req->item_price;
	   $experience->price_category=$req->price_category;
	   $experience->user_id = $req->user_id;
       $result=$experience->save();
	   
	   $listing_id=$experience->id;
	   if($result)
	      {
		  $response=$this->AddImages( $req ,$listing_id);
		  
		  if($response==null)
		  {
			 DB::rollBack();
				return response()->json([
					'message' => 'Experience not added',
					'status' => false,
					'data' => null,
				]); 
	   }
	   else
	   {
		   $list=$experience["images"] = $response;
		   DB::commit();
		    return response()->json(['status' => true,
		   'message'=> 'experience  saved',
		   'data'=> $experience
		   
		   ]);
		   
	    }
		
	   }
    }  
	
	   
	    
		
		function AddImages(Request $req ,$listing_id)
		{
			        $saved = array();
	            if($req->hasFile('image_url1'))
				{
					$data=$req->file('image_url1')->store('Images');
					$result = $this->saveImage($data, $listing_id);
					$saved[] = $result;
				}
				
				 if($req->hasFile('image_url2'))
				{
					$data=$req->file('image_url2')->store('Images');
					$result = $this->saveImage($data, $listing_id);
					$saved[] = $result;
				}
		       
			  if($req->hasFile('image_url3'))
	
{
					$data=$req->file('image_url3')->store('Images');
					$result = $this->saveImage($data, $listing_id);
					$saved[] = $result;
				}
				
				 if($req->hasFile('image_url4'))
				{
					$data=$req->file('image_url4')->store('Images');
					$result = $this->saveImage($data, $listing_id);
					$saved[] = $result;
				}
				
			  
				
				if(count($saved) > 0)
				{
					return $saved;
				}
			else
			{
				return null;
			}
		
}	
	private function saveImage($url, $listing_id){
			    $listing_image= new listing_image();
				$listing_image->image_width  = 0;
				$listing_image->image_height = 0;
                $listing_image->listing_id   = $listing_id;
				$listing_image->image_url = $url;
                $result =$listing_image->save();
				return $listing_image;
	}
	function getlist(Request $req)
            {
				
				$Rows_To_Fetch = 10;
				$off_set = $req->off_set; // 0, 1, 2, 3
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
							'message' => 'token is absent',
						]);      

                    }
					$experiences=Experience::skip($off_set)->take($Rows_To_Fetch)->get();
					
					foreach($experiences as $ex) {
						$exid = $ex->id;
						$images = Listing_Image::where('listing_id', $exid)->get();
						$ex["images"] = $images;
					}
					
				     return response()->json([
					'status' => true,
					'data' =>$experiences]);
		    }
}
		
	

			
	
		
		
		
		
		
		
		
