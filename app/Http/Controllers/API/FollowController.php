<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Follow;

class FollowController extends Controller
{
    public function following(Request $req){
    	return $req->user()->following()->with("user_following")->orderBy("created_at", "desc")->get();
    }

    public function follower(Request $req){
    	return $req->user()->follower()->with("user_followed")->orderBy("created_at", "desc")->get();
    }

    public function followToggle($id, Request $req){

    	$fill = [
    		"user_id" => $id,
    		"follower_id" => $req->user()->id
    	];

    	$follow = Follow::where($fill)->first();

    	if($follow){
    		$follow->delete();
    		return response()->json([
    			"status" => false,
            	"pesan" => "Berhasil meng-unfollow"
    		]);
    	} else {
    		Follow::create($fill);
    		return response()->json([
    			"status" => true,
            	"pesan" => "Berhasil mem-follow"
    		]);
    	}

    }

}
