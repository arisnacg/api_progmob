<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{
    protected $fillable = [
    	"postingan_id",
    	"user_id",
    	"isi"
    ];
}
