<?php

	namespace App\Http\Controllers;

	use Illuminate\Http\Request;
	use App\Models\Location;
	use App\Models\Experience;
	use App\Models\Listing_Image;
	use Illuminate\Support\Facades\Validator;
	use App\Models\User;
	use Illuminate\Support\Facades\DB;
	use Auth;
	use JWTAuth;
	use Tymon\JWTAuth\Exceptions\JWTException;

	class DataController extends Controller
	{
		
		//
		
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
		function getexperiencelist(Request $req)
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
				$off_set = $req->off_set; // 0, 1, 2, 3
				$off_set = $off_set * $Rows_To_Fetch;
				$term = '';
				
				$experiences=Experience
					::select('experiences.id', 'experiences.item_title',
					'experiences.item_price','experiences.price_category'
					,'experiences.isFeature')
					->skip($off_set)->take($Rows_To_Fetch)->get();
					
				if($req->has('term')){
					$term = $req->term;
					$experiences=Experience
					::where('experiences.item_title', 'like', "%$term%")
					->orWhere('experiences.item_description', 'like', '%term%')
					->select('experiences.id', 'experiences.item_title',
					'experiences.item_price','experiences.price_category'
					,'experiences.isFeature')
					->skip($off_set)->take($Rows_To_Fetch)->get();
				}
				
				foreach ($experiences as $exp){
					$eid = $exp->id;
					$images = Listing_Image
					::where('listing_id', '=', $eid)->get();
					$exp->images = $images;
				}
				return response()->json([
						'status' => true,
						'data' =>$experiences]);
			}
		
									
		}
				
				

			
		

				
		
			
			
			
			
			
			
			
