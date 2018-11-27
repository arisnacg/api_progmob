<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Postingan;
use App\Like;
use App\Komentar;
use Validator;

class PostinganController extends Controller
{
    public function index(){
    	return response()->json(
    		Postingan::with("user")
    		->withCount([
    			"like",
    			"komentar"
    		])
    		->get()
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

    public function store(Request $req){
    	$validator = Validator::make($req->all(), [
            'konten' => 'required|string'
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
            'konten' => 'required|string'
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
        	"status" => false,
        	"pesan" => "Postingan berhasil di-dihapus"
        ]);
    }
}
