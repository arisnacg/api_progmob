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
    //Get all user
    public function getAllUser(){
        return User::all();
    }
    /////////////////////////////////////////////////////////////////////////////
    //Coba
    public function coba(){
        return 123;
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
	        	"user_id" => $user->id
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

        return response()->json([
        	"status" => true,
        	"api_token" => $fill["api_token"],
        	"pesan" => "Register berhasil",
        	"user_id" => $user->id
        ]);
    }

    public function generateToken(){
    	return str_random(60);
    }
}
