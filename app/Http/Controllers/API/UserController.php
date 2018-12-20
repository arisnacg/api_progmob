<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Validator;
use Auth;

class UserController extends Controller
{
    /////////////////////////////////////////////////////////////////////////////
    //Ganti foto profil
    public function gantiFotoProfil(Request $req){
        $validator = Validator::make($req->all(), [
            'img' => 'required|image|mimes:jpeg,png,jpg|max:1024',
        ], [
            "img.required" => "File gambar tidak boleh kosong",
            "img.image" => "File yang diupload harus berupa gambar",
            "img.mimes" => "Format file gambar harus jpeg, jpg atau png",
            "img.max" => "Besar file gambar maksimal 1MB"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "pesan" => $validator->errors()->first()
            ]);            
        }

        $filenametostore = "";
        if($req->hasFile('img')) {
            //get filename with extension
            $filenamewithextension = $req->file('img')->getClientOriginalName();
            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
            //get file extension
            $extension = $req->file('img')->getClientOriginalExtension();
            //filename to store
            $filenametostore = uniqid().'.'.$extension;
            $req->file('img')->move('foto_profil', $filenametostore);
            //file_put_contents('foto_profil/'.$filenametostore, $req->file('img'));
            $req->user()->update(["foto_profil" => $filenametostore]);
            return response()->json([
                "status" => true,
                "pesan" => "Foto Profil berhasil di-ganti",
                "foto_profil" => $filenametostore
            ]);
        }
        return response()->json([
            "status" => false,
            "pesan" => "Foto Profil gagal di-ganti",
            "foto_profil" => $filenametostore
        ]); 
    }

    /////////////////////////////////////////////////////////////////////////////
    //Get user detail
    public function getUser($id){
        return User::with([
            "postingan" => function($query){
                $query->orderBy("created_at", "desc");
            }
        ])
        ->withCount([
            "follower",
            "following"
        ])
        ->find($id);
    }
    /////////////////////////////////////////////////////////////////////////////
    //Update profil user
    public function update(Request $req){
        $validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'nama' => 'required'
        ], [
            "email.required" => "Email tidak boleh kosong",
            "email.email" => "Email tidak valid",
            "nama.required" => "Nama tidak boleh kosong"
        ]);

        if ($validator->fails()) {
            return response()->json([
            	"status" => false,
            	"pesan" => $validator->errors()->first()
            ]);            
        }

        $req->user()->update($req->all());

        return response()->json([
        	"status" => true,
        	"pesan" => "Profil berhasil di-update"
        ]);
    }
    /////////////////////////////////////////////////////////////////////////////
    //Get all user
    public function getAllUser(Request $req){
        $data_user = User::with("follower")
        ->get();
        $data = [];
        if(count($data_user)){
            foreach($data_user as $user){
                $followed = false;
                if(count($user->follower)){
                    foreach($user->follower as $follower){
                        if($follower->follower_id == $req->user()->id){
                            $followed = true;
                            break;
                        }
                    }
                }
                $user_baru = $user;
                $user_baru["followed"] = $followed;
                $data[] = $user_baru;
            }
        }

        return $data;
    }
    /////////////////////////////////////////////////////////////////////////////
    //Login dengan ID dan Token
    public function loginIdToken($id, $api_token){
        $user = User::where("api_token", $api_token)->find($id);
        if($user){
            $user->update([
                "api_token" => $api_token
            ]);
            return $user;
        } else {
            return response()->json([
                "pesan" => "User tidak dengan token ".$api_token." ditemukan"
            ], 401);
        }
    }
    /////////////////////////////////////////////////////////////////////////////
    //Logout
    public function logout(Request $req){
        $user = Auth::user();
        $user->api_token = null;
        $user->save();
        return response()->json([
            "status" => true,
            "api_token" => null,
            "pesan" => "Logout berhasil",
            "user_id" => 0
        ]);
    }
	/////////////////////////////////////////////////////////////////////////////
	//Get User
	public function user(Request $req){
		return $req->user();
	}
	/////////////////////////////////////////////////////////////////////////////
	//Login
	public function login(Request $req){
		$validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ], [
            "email.required" => "Email tidak boleh kosong",
            "email.email" => "Email tidak valid",
            "password.required" => "Password tidak boleh kosong"
        ]);

        if ($validator->fails()) {
            return response()->json([
            	"status" => false,
            	"pesan" => $validator->errors()->first()
            ]);            
        }

        $cek = Auth::attempt([
        	'email' => $req->email,
        	'password' => $req->password
        ]);

        if($cek){
            $user = Auth::user();
            $user->update(["api_token" => $this->generateToken()]);
            return response()->json([
	        	"status" => true,
                "api_token" => $user->api_token,
	        	"pesan" => "Login berhasil",
                "user_id" => $user->id,
                "user_nama" => $user->nama,
                "user_email" => $user->email,
                "foto_profil" => $user->foto_profil
	        ]);

        } else {
            return response()->json([
            	"status" => false,
            	"pesan" => "User tidak ditemukan"
            ]);  
        }

	}
	/////////////////////////////////////////////////////////////////////////////
	//Register
    public function register(Request $req){
    	$validator = Validator::make($req->all(), [
            'nama' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'c_password' => 'required',
        ], [
            "email.required" => "Email tidak boleh kosong",
            "email.email" => "Email tidak valid",
            "password.required" => "Password tidak boleh kosong",
            "password.min" => "Password minimal memiliki 6 karakter",
            "c_password.required" => "Password harus dikonfirmasi",
            "c_password.same" => "Konfirmasi password tidak cocok"
        ]);



        if ($validator->fails()) {
            return response()->json([
            	"status" => false,
            	"pesan" => $validator->errors()->first()
            ]);            
        }

        $fill = $req->all();
        $fill["api_token"] = $this->generateToken();
        $fill["password"] = bcrypt($req->password);
        $user = User::create($fill);

        Auth::loginUsingId(1, $user->id);

        return response()->json([
        	"status" => true,
        	"api_token" => $fill["api_token"],
        	"pesan" => "Register berhasil",
            "user_id" => $user->id,
            "user_nama" => $user->nama,
            "user_email" => $user->email,
            "foto_profil" => $user->foto_profil
        ]);
    }

    public function generateToken(){
    	return str_random(60);
    }
}
