<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Komentar;
use Validator;

class KomentarController extends Controller
{

	public function show($id){
		return response()->json(
			Komentar::where("postingan_id", $id)->get()
		);
	}

   public function store(Request $req){
   		$validator = Validator::make($req->all(), [
            'isi' => 'required|string',
            'postingan_id' => "required"
        ], [
            "konten.required" => "Komentar tidak boleh kosong"
        ]);

     	if ($validator->fails()) {
            return response()->json([
            	"status" => false,
            	"pesan" => $validator->errors()->first()
            ]);            
        }

        $fill = $req->all();
        $fill["user_id"] = $req->user()->id;
        $postingan = Komentar::create($fill);

        if($postingan){
        	return response()->json([
            	"status" => true,
            	"pesan" => "Komentar berhasil ditambahkan"
            ]);  
        }
        return response()->json([
        	"status" => false,
        	"pesan" => "Komentar gagal ditambahkan"
        ]);
   }

   public function update(Request $req, $id){
   		$validator = Validator::make($req->all(), [
            'isi' => 'required|string',
            'postingan_id' => "required"
        ], [
            "isi.required" => "Komentar tidak boleh kosong"
        ]);

     	if ($validator->fails()) {
            return response()->json([
            	"status" => false,
            	"pesan" => $validator->errors()->first()
            ]);            
        }

        $fill = $req->all();
        $postingan = Komentar::findOrFail($id)->update($fill);

        if($postingan){
        	return response()->json([
            	"status" => true,
            	"pesan" => "Komentar berhasil di-update"
            ]);  
        }
        return response()->json([
        	"status" => false,
        	"pesan" => "Komentar gagal di-update"
        ]);
   }

   public function destroy($id){

     	Komentar::findOrFail($id)->delete();

        return response()->json([
        	"status" => false,
        	"pesan" => "Komentar berhasil di-dihapus"
        ]);
    }

}
