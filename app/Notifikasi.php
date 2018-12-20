<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = "notif";
    

    public function user(){
    	return $this->belongsTo(User::class, "user_id");
    }
}
