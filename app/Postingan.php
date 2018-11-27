<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Postingan extends Model{
    protected $fillable = [
    	"user_id",
    	"konten"
    ];

    public function user(){
    	return $this->belongsTo(User::class, "user_id");
    }

    public function like(){
    	return $this->hasMany(Like::class, "postingan_id");
    }

    public function komentar(){
    	return $this->hasMany(Komentar::class, "postingan_id");
    }
}
