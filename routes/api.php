<?php

use Illuminate\Http\Request;

Route::post("login", "API\UserController@login");
Route::post("register", "API\UserController@register");
Route::get("coba", "API\UserController@coba");
Route::get("user/all", "API\UserController@getAllUser");
Route::get("login_user/{id}/{api_token}", "API\UserController@loginIdToken");

Route::group(['middleware' => 'auth:api'], function () {
    Route::get("user", "API\UserController@user");
    Route::post("logout", "API\UserController@logout");
    Route::resource("postingan", "API\PostinganController");
    Route::post("like/toggle/{postingan_id}", "API\LikeController@toggle");
    Route::resource("komentar", "API\KomentarController");
});
