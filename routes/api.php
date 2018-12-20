<?php

use Illuminate\Http\Request;

Route::post("login", "API\UserController@login");
Route::post("register", "API\UserController@register");
Route::get("coba", "API\UserController@coba");
Route::get("login_user/{id}/{api_token}", "API\UserController@loginIdToken");

Route::group(['middleware' => 'auth:api'], function () {
    Route::get("user", "API\UserController@user");
    Route::put("user", "API\UserController@update");
    Route::post("logout", "API\UserController@logout");
    Route::resource("postingan", "API\PostinganController");
    Route::get("postingan/{id}/komentar", "API\PostinganController@komentar");
    Route::get("user/postingan", "API\PostinganController@userPostingan");
    Route::post("like/toggle/{postingan_id}", "API\LikeController@toggle");
    Route::resource("komentar", "API\KomentarController");
    Route::get("/following", "API\FollowController@following");
    Route::get("/follower", "API\FollowController@follower");
    Route::post("/follow/{id}", "API\FollowController@followToggle");
    Route::get("user/all", "API\UserController@getAllUser");
    Route::get("user/get/{id}", "API\UserController@getUser");
    Route::post("/user/fotoprofil", "API\UserController@gantiFotoProfil");
});
