<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Postingan;
use App\Like;
use App\Komentar;
use Validator;
use DB;

class PostinganController extends Controller
{


	public function userPostingan(Request $req){

		$data_postingan = Postingan::with(["user", "like"])
		->where("user_id", $req->user()->id)
    	->withCount([
			"like",
			"komentar"
		])
		->orderBy("created_at", "desc")
		->get();

        $postingan = [];

        if(count($data_postingan)){
            foreach ($data_postingan as $pos) {
                $liked = false;
                if(count($pos->like)){
                    foreach($pos->like as $like){
                        if($like->user_id == $req->user()->id){
                            $liked = true;
                            break;
                        }
                    }
                }
                $pos_baru = $pos;
                $pos_baru["liked"] = $liked;
                unset($pos_baru["like"]);
                $postingan[] = $pos_baru;
            }
        }
        

        return response()->json(
            $postingan
        );
	}

    public function index(Request $req){

        $user_id = $req->user()->id;

        $data_postingan = Postingan::with(["user", "like"])
        ->withCount([
            "like",
            "komentar"
        ])
        ->where("user_id", $user_id)
        ->orWhere(function($query) use ($user_id){
            //postingan
            $query->whereHas("user", function($query) use ($user_id){
                //user
                $query->whereHas("follower", function($query) use ($user_id){
                    //follow
                    $query->where("follower_id", $user_id);
                });
            });
        })
        ->orderBy("created_at", "desc")
        ->get();

		$postingan = [];

		if(count($data_postingan)){
			foreach ($data_postingan as $pos) {
				$liked = false;
				if(count($pos->like)){
					foreach($pos->like as $like){
						if($like->user_id == $req->user()->id){
							$liked = true;
							break;
						}
					}
				}
				$pos_baru = $pos;
				$pos_baru["liked"] = $liked;
				unset($pos_baru["like"]);
				$postingan[] = $pos_baru;
			}
		}
		

    	return response()->json(
    		$postingan
    	);
    }

    public function show($id){
    	return response()->json(
    		Postingan::withCount([
    			"like",
    			"komentar"
    		])
    		->findOrFail($id)
    	);
    }

    public function komentar($id){
    	$komentar = Komentar::with("user")
    	->where("postingan_id", $id)
    	->get();
    	return $komentar;
    }

    public function store(Request $req){
    	$validator = Validator::make($req->all(), [
            'konten' => 'required'
        ], [
            "konten.required" => "Postingan tidak boleh kosong"
        ]);

     	if ($validator->fails()) {
            return response()->json([
            	"status" => false,
            	"pesan" => $validator->errors()->first()
            ]);            
        }

        $fill = $req->all();
        $fill["user_id"] = $req->user()->id;
        $postingan = Postingan::create($fill);

        if($postingan){
        	return response()->json([
            	"status" => true,
            	"pesan" => "Postingan berhasil di-posting"
            ]);  
        }
        return response()->json([
        	"status" => false,
        	"pesan" => "Postingan gagal di-posting"
        ]);
    }

    public function update(Request $req, $id){
    	$validator = Validator::make($req->all(), [
            'konten' => 'required'
        ], [
            "konten.required" => "Postingan tidak boleh kosong"
		]);

     	if ($validator->fails()) {
            return response()->json([
            	"status" => false,
            	"pesan" => $validator->errors()->first()
            ]);            
        }

        $fill = $req->all();
        $postingan = Postingan::findOrFail($id)->update($fill);

        if($postingan){
        	return response()->json([
            	"status" => true,
            	"pesan" => "Postingan berhasil di-update"
            ]);  
        }
        return response()->json([
        	"status" => false,
        	"pesan" => "Postingan gagal di-update"
        ]);
    }

    public function destroy($id){

     	Like::where("postingan_id", $id)->delete();
     	Komentar::where("postingan_id", $id)->delete();
		Postingan::findOrFail($id)->delete();
		

        return response()->json([
        	"status" => true,
        	"pesan" => "Postingan berhasil di-dihapus"
        ]);
	}
	
}
