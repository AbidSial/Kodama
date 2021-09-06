<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Experience;
use App\Models\Listing_Image;

class Homecontroller extends Controller
{
    //
	private $Rows_To_Fetch = 5;
	function homeExperiences(Request $req)
	{
		$featured_experiences = $this->getTopFeaturedItems();
		$nearby_experiences = $this->getNearbyItems();
		 return response()->json(['status'=>true,
		'message'=>"List obtained",
		'data' => ['featured_experiences'=> $featured_experiences, "nearby_experiences"=> $nearby_experiences]
		]);
	}
			
		
		private function getTopFeaturedItems(){
			 $experiences=Experience::where('isfeature',true)->take($this->Rows_To_Fetch)->get();
			 foreach($experiences as $ex) {
				$exid = $ex->id;
				$images = Listing_Image::where('listing_id', $exid)->get();
				$ex["images"] = $images;
			 }
			 return $experiences;
		}
		private function getNearbyItems()
		{
			$experiences=Experience::where('isfeature',false)->take($this->Rows_To_Fetch)->get();
		
			foreach($experiences as $ex) {
				$exid = $ex->id;
				$images = Listing_Image::where('listing_id', $exid)->get();
				$ex["images"] = $images;
			}
			return $experiences;
		
		}
}
