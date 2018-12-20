<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama', 'email', 'password', 'api_token', 'foto_profil'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];

    public function postingan(){
        return $this->hasMany(Postingan::class, "user_id");
    }

    public function follower(){
        return $this->hasMany(Follow::class, "user_id");
    }

    public function following(){
        return $this->hasMany(Follow::class, "follower_id");
    }
}
