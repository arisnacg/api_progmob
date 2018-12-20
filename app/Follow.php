<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $fillable = ["user_id", "follower_id"];

    public function user_following(){
    	return $this->belongsTo(User::class, "user_id");
    }

    public function user_followed(){
    	return $this->belongsTo(User::class, "follower_id");
    }
}
