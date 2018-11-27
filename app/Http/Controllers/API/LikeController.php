<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Like;

class LikeController extends Controller
{
    public function toggle(Request $req, $postingan_id){
    	$like = Like::where([
    		"postingan_id" => $postingan_id,
    		"user_id" => $req->user()->id
    	])
    	->first();
    	$liked = true;
    	if(isset($like)){
    		$like->delete();
    		$liked = false;
    	} else {
    		$like = Like::create([
    			"postingan_id" => $postingan_id,
    			"user_id" => $req->user()->id
    		]);
    	}
    	return response()->json([
    		"liked" =>$liked,
    		"pesan" => ($liked)? "Postingan di-like" : "Postingan di-unlike"
    	]);
    }
}
